<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{block name='title'}{$appName|escape}{/block}</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header class="site-header">
    <a class="site-logo" href="/">{$appName|escape}</a>
    <nav class="site-nav" aria-label="Main navigation">
        <a href="/">Home</a>
        <a href="/api/docs">API Docs</a>
    </nav>
</header>

<main class="site-main">
    {block name='content'}{/block}
</main>

<footer class="site-footer">
    <p>&copy; {$smarty.now|date_format:'%Y'} {$appName|escape}</p>
</footer>

<script src="/js/main.js"></script>
</body>
</html>
