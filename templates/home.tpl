{extends file='layouts/app.tpl'}

{block name='title'}Home - {$appName|escape}{/block}

{block name='content'}
    <section class="page-heading" data-testid="home-page">
        <p class="page-heading__eyebrow">Blog</p>
        <h1>Latest articles by category</h1>
    </section>

    {if empty($categories)}
        <section class="empty-state" data-testid="empty-state">
            <h2>No articles yet</h2>
            <p>Run database seeders to populate the blog.</p>
        </section>
    {else}
        <div class="category-list">
            {foreach $categories as $category}
                <section class="category-section" data-testid="category-section">
                    <div class="category-section__header">
                        <div>
                            <h2>{$category.name|escape}</h2>
                            <p>{$category.description|escape}</p>
                        </div>
                        <a class="button" href="/category/{$category.slug|escape:'url'}">All articles</a>
                    </div>

                    <div class="post-grid">
                        {foreach $category.posts as $post}
                            {include file='partials/post-card.tpl' post=$post}
                        {/foreach}
                    </div>
                </section>
            {/foreach}
        </div>
    {/if}
{/block}
