{extends file='layouts/app.tpl'}

{block name='title'}Page not found - {$appName|escape}{/block}

{block name='content'}
    <section class="empty-state" data-testid="not-found-page">
        <p class="page-heading__eyebrow">404</p>
        <h1>Page not found</h1>
        <p>The requested page does not exist or has been moved.</p>
        <a class="button" href="/">Back to home</a>
    </section>
{/block}
