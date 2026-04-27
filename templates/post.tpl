{extends file='layouts/app.tpl'}

{block name='title'}{$post.title|escape} - {$appName|escape}{/block}

{block name='content'}
    <article class="post-page" data-testid="post-page">
        <header class="post-page__header">
            <a class="back-link" href="/">Back to home</a>
            <div class="post-page__categories">
                {foreach $post.categories as $category}
                    <a href="/category/{$category.slug|escape:'url'}">{$category.name|escape}</a>
                {/foreach}
            </div>
            <h1>{$post.title|escape}</h1>
            <p>{$post.description|escape}</p>
            <div class="post-page__meta">
                <span>{$post.published_at|date_format:'%d.%m.%Y'}</span>
                <span>{$post.views|escape} views</span>
            </div>
        </header>

        {if !empty($post.image)}
            <img class="post-page__image" src="{$post.image|escape}" alt="">
        {/if}

        <div class="post-page__content">
            {$post.content|escape|nl2br}
        </div>
    </article>

    <section class="similar-posts">
        <div class="category-section__header">
            <h2>Similar articles</h2>
        </div>
        {if empty($similar)}
            <p>No similar articles yet.</p>
        {else}
            <div class="post-grid">
                {foreach $similar as $post}
                    {include file='partials/post-card.tpl' post=$post}
                {/foreach}
            </div>
        {/if}
    </section>
{/block}
