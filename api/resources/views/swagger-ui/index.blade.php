<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $data['title'] ?? (config('app.name') . ' - ' . __('Swagger')) }}</title>

        <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@latest/swagger-ui.css">

        <style>
            html {
                box-sizing: border-box;
            }

            *, *:before, *:after {
                box-sizing: inherit;
            }

            body {
                margin: 0;
                background: #fafafa;
            }
        </style>

        @if (! empty($data['stylesheet']))
            <style>{{ file_get_contents($data['stylesheet']) }}</style>
        @endif
    </head>
    <body>
        <div id="swagger-ui"></div>

        <script src="https://unpkg.com/swagger-ui-dist@latest/swagger-ui-bundle.js"></script>
        <script src="https://unpkg.com/swagger-ui-dist@latest/swagger-ui-standalone-preset.js"></script>

        <script>
            const csrfCookieName = 'XSRF-TOKEN';
            const registrationPath = '/api/v2/auth/sign-up';

            function getCookie(name) {
                const prefix = `${name}=`;
                const cookie = document.cookie
                    .split('; ')
                    .find((item) => item.startsWith(prefix));

                return cookie ? decodeURIComponent(cookie.substring(prefix.length)) : null;
            }

            async function addCsrfToken(request) {
                request.credentials = 'include';

                const requestUrl = new URL(request.url, window.location.origin);

                if (request.method?.toUpperCase() !== 'POST' || requestUrl.pathname !== registrationPath) {
                    return request;
                }

                const csrfUrl = new URL('/sanctum/csrf-cookie', requestUrl.origin);

                const csrfResponse = await fetch(csrfUrl, {
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                        Accept: 'application/json',
                    },
                });

                if (!csrfResponse.ok) {
                    throw new Error(`Unable to initialize CSRF cookie: HTTP ${csrfResponse.status}`);
                }

                const token = getCookie(csrfCookieName);

                if (!token) {
                    throw new Error('XSRF-TOKEN cookie was not set');
                }

                request.headers = request.headers || {};
                request.headers['X-XSRF-TOKEN'] = token;

                return request;
            }

            window.onload = function () {
                window.ui = SwaggerUIBundle({
                    urls: [
                        @foreach ($data['versions'] as $version => $path)
                            {
                                url: '{{ url("{$data['path']}/{$version}") }}',
                                name: '{{ $version }}',
                            },
                        @endforeach
                    ],
                    "urls.primaryName": "{{ $data['default'] }}",
                    dom_id: '#swagger-ui',
                    deepLinking: true,
                    presets: [
                        SwaggerUIBundle.presets.apis,
                        SwaggerUIStandalonePreset
                    ],
                    layout: 'StandaloneLayout',
                    requestInterceptor: addCsrfToken,
                    @if (!is_null($data["validator_url"]))
                        validatorUrl: '{{ $data["validator_url"] }}',
                    @endif
                    oauth2RedirectUrl: '{{ url("{$data['path']}/oauth2-redirect") }}',
                });

                ui.initOAuth({
                    clientId: '{{ $data['oauth']['client_id'] }}',
                    clientSecret: '{{ $data['oauth']['client_secret'] }}',
                });
            };
        </script>
    </body>
</html>
