<?php
/**
 * Author: miro@keboola.com
 * Date: 14/09/2017
 */

namespace Keboola\OAuthV2Bundle\Tests;

use Doctrine\DBAL\Connection;
use Keboola\Syrup\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

class OAuthControllerTest extends WebTestCase
{
    /** @var Connection */
    protected $connection;

    private $testComponentId = 'leochan.ex-quickbooks';

    /** @var Client */
    protected $client;

    public function setUp()
    {
        self::bootKernel(['debug' => true]);
        $container = static::$kernel->getContainer();
        $this->connection = $container->get('doctrine')->getConnection('oauth_providers');
        $this->connection->exec(
            "TRUNCATE `consumers`"
        );
        $this->connection->exec(
            "TRUNCATE `credentials`"
        );
        $this->connection->exec(
            "TRUNCATE `sessions`"
        );

        $this->client = static::createClient();

        // register API
        $api = [
            'component_id' => $this->testComponentId,
            'friendly_name' => 'Quickbooks Report',
            'app_key' => getenv('APP_KEY'),
            'auth_url' => 'https://appcenter.intuit.com/connect/oauth2?response_type=code&client_id=%%client_id%%&scope=com.intuit.quickbooks.accounting com.intuit.quickbooks.payment&redirect_uri=%%redirect_uri%%&state=security_token12345',
	        'token_url' => 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer',
            'oauth_version' => 'quickbooks'
        ];

        $api['app_secret_docker'] = 'not needed';
        $api['app_secret'] = $container
            ->get('syrup.encryption.base_wrapper')
            ->encrypt(getenv('APP_SECRET'));

        $this->connection->insert('consumers', (array) $api);
    }

    public function testInitAction()
    {
        $params = [
            'id' => '123456',
            'token' => '123456',
            'appKey' => '123456',
            'appSecret' => '123456',
            'authorizedFor' => 'test',
            'returnUrl' => 'callback'
        ];

        $sessionMock = $this->getMockBuilder('Keboola\OAuthV2Bundle\Storage\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $bagMock = new AttributeBag();
        $bagMock->initialize($params);
        $sessionMock->method('getBag')
            ->willReturn($bagMock);

        $this->client->getContainer()->set('oauth.session', $sessionMock);

        $container = static::$kernel->getContainer();
        $container->set('oauth.session', $sessionMock);

        $server = [
            'HTTP_X-KBC-ManageApiToken' => MANAGE_API_TOKEN
        ];

        /** @var Crawler $crawler */
        $crawler = $this->client->request(
            'POST', '/oauth-v2/authorize/' . $this->testComponentId,
            $params,
            [],
            $server
        );

        /** @var RedirectResponse $response */
        $response = $this->client->getResponse();

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertContains(
            'Redirecting to https://appcenter.intuit.com/connect/oauth2',
            $crawler->getNode('html')->nodeValue
        );
        $this->assertContains(
            'client_id=123456',
            $crawler->getNode('html')->nodeValue
        );
    }
}

