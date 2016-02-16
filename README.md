FORMAT: 1A
HOST: http://syrup.keboola.com/oauth-v2

# OAuth Manager
Create and manage Credentials for API resources utilizing OAuth 1.0 and 2.0

This API aims to handle all OAuth authorization processes. This list of supported APIs is managed by KBC Dev team

The authorization process can be initiated manually to retrieve OAuth tokens for development/testing purposes.

Using a `POST` request with `token` and `id` will store the result in the application database, encrypted by Keboola Docker application with matching `componentId`. The component must support encryption. After storing the result, the application redirects to `HTTP_REFERRER`, which can be overriden by using `returnUrl` parameter in the POST request.

The `GET` request for "manual" authentication returns the raw contents of `#data`, but does **not** store the result.

The `X-KBC-ManageApiToken` must have `oauth:manage` scope.

## TODO
- scope is missing

# Group Authorize

## Generate OAuth token for OAuth applications [/authorize/{componentId}{?token,id,description,returnUrl}]

### Get a TEST token in browser [GET]

+ Parameters

    + componentId = `` (required, string, `wr-dropbox`) ... Identifier of the KBC component

+ Response 201 (application/json)

        {
          "access_token": "dsjioafhoiy832yt598y7895y",
          "refresh_token": "kf98v0894u8j580jy8902xyjciurewc",
          "token_type": "Bearer"
        }


### Generate token from a web form/UI [POST]

+ Parameters

    + componentId = `` (required, string, `yourApp`) ... Identifier of the KBC component

    + token = `` (required, string, `305-78945-rg48re4g86g48gwgr48e6`) ... Your KBC Token

    + id = `` (required, string, `main`) ... Credentials configuration identifier to be saved with the result

    + authorizedFor = `` (optional, string, `Someone's account`) ... Credentials description (eg. account name)

    + returnUrl = `` (optional, string, `http://back.to.ui`) ... Override the HTTP Referrer

+ Request (multipart/form-data; boundary=----WebKitFormBoundaryC5GD12ZfR1D8yZIt)
    + Body

            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt
            Content-Disposition: form-data; name="token"

            305-78954-d54f6ew4f84ew6f48ewq4f684q
            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt--

            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt
            Content-Disposition: form-data; name="id"

            main
            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt--

            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt
            Content-Disposition: form-data; name="authorizedFor"

            Credentials description (eg. account name)
            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt--

    + Schema

            {
                "type": "object",
                "required": true,
                "properties": {
                    "id": {
                        "type": "string",
                        "required": true
                    },
                    "token": {
                        "type": "string",
                        "required": true
                    },
                    "componentId": {
                        "type": "string",
                        "required": true
                    },
                    "authorizedFor": {
                        "type": "string",
                        "required": false
                    }
                }
            }

+ Response 302

# Group Credentials

## Retrieve credentials [/credentials/{componentId}/{id}]

### Get Credentials [GET]

+ Parameters

    + componentId = `` (required, string, `yourApp`) ... Identifier of the KBC component

+ Request (application/json)

    + Headers

            Accept: application/json
            X-StorageApi-Token: Your-Sapi-Token

+ Response 201 (application/json)

        {
            "id": "main",
            "authorizedFor": "Myself",
            "creator": {
                "id": "1234",
                "description": "me@keboola.com"
            },
            "created": "2016-01-31 00:13:30",
            "#data": "KBC::ComponentProjectEncrypted==F2LdyHQB45lJHtf6dZbxzfuhqlplplplpxByYn19OJQ3JLpyoxQcEqovAzipNAd6Kxc0PDe2nP468448648aSN0OzJcE4VovJk7YaX8wrzoS+tURTRFuWVFZZ5gpJjQvezj1iOgm++KOROIHNdfohuiogygiwpOuNgPFzgYeg8RL5CCBAB5zzVr4HmT4/DtVdOV+cJ5miPmuHp93sMQA=",
            "oauthVersion": "2.0",
            "appKey": "w51u7j30oghe412",
            "#appSecret": "KBC::ComponentEncrypted==/5fEM59+3+59+5+uE/TQPwltR8mp+WNu7kYAJkdTXxkc2UswH0YrnAYRjeGTqGp5hA=="
        }

### Delete credentials [DELETE]

+ Request

    + Headers

            Accept: application/json
            X-StorageApi-Token: Your-Sapi-Token


+ Response 204

## List credentials [/credentials/{componentId}]

### Get Credentials list for the project [GET]

+ Parameters

    + componentId = `` (required, string, `yourApp`) ... Identifier of the KBC component

+ Request

    + Headers

            Accept: application/json
            X-StorageApi-Token: Your-Sapi-Token

+ Response 200 (application/json)

        [
            {
                "authorizedFor": "test",
                "id": "main",
                "creator": {
                    "id": "1234",
                    "description": "me@keboola.com"
                },
                "created": "2016-01-31 00:13:30"
            }
        ]

# Group Manage

## List supported API [/manage]

### Get list of supported APIs [GET]

+ Request

    + Headers

            Accept: application/json
            X-KBC-ManageApiToken: Manage-token

+ Response 200 (application/json)

        [
          {
            {
                "id": "ex-adwords",
                "friendly_name": "AdWords Extractor",
                "app_key": "377121273700-ee1mrkdknfarq5k74pc7qs2g6upqos05.apps.googleusercontent.com",
                "oauth_version": "2.0"
            },
            {
                "id": "ex-dropbox",
                "friendly_name": "Dropbox Extractor",
                "app_key": "519fh4amnds4sx9",
                "oauth_version": "2.0"
            },
            {
                "id": "wr-dropbox",
                "friendly_name": "Dropbox Writer",
                "app_key": "i60qx79tjxdd6v3",
                "oauth_version": "2.0"
            }
          }
        ]

## Get API detail [/manage/{componentId}]

### Get API detail [GET]

+ Parameters

    + componentId = `` (required, string, `yourApp`) ... Identifier of the KBC component

+ Request

    + Headers

            Accept: application/json
            X-KBC-ManageApiToken: Manage-token

+ Response 200 (application/json)

        {
            "component_id": "wr-dropbox",
            "friendly_name": "Dropbox Writer",
            "app_key": "w51y7j30ovhe412",
            "app_secret_docker": "KBC::ComponentEncrypted==/fiewoqhfhoighuqg64gr426wg4864rc26g4re6g/TQPwltR8mp+FEWFwg6gr6e4gc688g6w42==",
            "oauth_version": "2.0"
        }

## Add API [/manage]

### Add new component [POST]

+ Parameters

    + component_id = `` (required, string, `wr-dropbox`) ... Identifier of the KBC component

    + friendly_name = `` (required, string, `Your Application`) ... Name of the application

    + app_key = `` (required, string, `as1d23f48`) ... Consumer Key / Client ID

    + app_secret = `` (required, string, `f86q4f6e4f64q6f486q`) ... Consumer Secret / Client secret. Stored encrypted

    + auth_url = `` (required, string, `https://www.dropbox.com/1/oauth2/authorize?response_type=code&client_id=%%client_id%%&redirect_uri=%%redirect_uri%%&state=%%hash%%`) ... For **1.0**, `%%oauth_token%%` is replaced by `oauth_token` retrieved by initial POST to API. For **2.0**, `%%redirect_uri%%` and `%%client_id%%` are replaced by generated redirect URI (to the OAuth application) and `app_key`.

    + token_url = `` (required, string, `https://api.dropbox.com/1/oauth2/token`) ... Access token url. Used in the last step of OAuth process

    + request_token_url = `` (optional, string, `https://api.twitter.com/oauth/request_token`) ... Required for OAuth 1.0

    + oauth_version: `2.0` (enum[string], required) - OAuth version

        + Members
            + `1.0` - For OAuth 1.0
            + `2.0` - For OAuth 2.0

+ Request (application/json)

    + Headers

            Accept: application/json
            X-StorageApi-Token: Your-Sapi-Token
            X-KBC-ManageApiToken: Manage-token

    + Body

        {
            "component_id": "wr-dropbox",
            "friendly_name": "Dropbox Writer",
            "app_key": "yourAppKey",
            "app_secret": "yourAppSecret",
            "auth_url": "https://www.dropbox.com/1/oauth2/authorize?response_type=code&client_id=%%client_id%%&redirect_uri=%%redirect_uri%%",
            "token_url": "https://api.dropbox.com/1/oauth2/token",
            "oauth_version": "2.0"
        }

    + Schema

            {
                "type": "object",
                "required": true,
                "properties": {
                    "component_id": {
                        "type": "string",
                        "required": true
                    },
                    "friendly_name": {
                        "type": "string",
                        "required": true
                    },
                    "app_key": {
                        "type": "string",
                        "required": true
                    },
                    "app_secret": {
                        "type": "string",
                        "required": true
                    },
                    "auth_url": {
                        "type": "string",
                        "required": true,
                        "description": "For **1.0**, `%%oauth_token%%` is replaced by `oauth_token` retrieved by initial POST to API. For **2.0**, `%%redirect_uri%%` and `%%client_id%%` are replaced by generated redirect URI (to the OAuth application) and `app_key`."
                    },
                    "token_url": {
                        "type": "string",
                        "required": true
                    },
                    "request_token_url": {
                        "type": "string",
                        "required": false,
                        "description": "Required for OAuth 1.0"
                    },
                    "oauth_version": {
                        "type": "string",
                        "required": true
                    }
                }
            }

+ Response 200 (application/json)

        {
            "status": "created",
            "component_id": "wr-dropbox"
        }


## Delete API [/manage/{componentId}]

### Delete component [DELETE]

+ Parameters

    + componentId = `` (required, string, `yourApp`) ... Identifier of the KBC component

+ Request

    + Headers

            Accept: application/json
            X-KBC-ManageApiToken: Manage-token

+ Response 204



