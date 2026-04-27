<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>API Docs - {$appName|escape}</title>
    <link rel="stylesheet" href="/docs/assets/swagger-ui.css">
    <link rel="icon" type="image/png" href="/docs/assets/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/docs/assets/favicon-16x16.png" sizes="16x16">
</head>
<body>
<div id="swagger-ui"></div>
<script src="/docs/assets/swagger-ui-bundle.js"></script>
<script src="/docs/assets/swagger-ui-standalone-preset.js"></script>
<script>
    window.addEventListener('load', () => {
        window.ui = SwaggerUIBundle({
            url: '/docs/openapi.yaml',
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],
            layout: 'StandaloneLayout'
        });
    });
</script>
</body>
</html>
