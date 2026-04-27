{extends file='layouts/app.tpl'}

{block name='title'}Home - {$appName|escape}{/block}

{block name='content'}
    {if empty($categories)}
        <section class="empty-state" data-testid="empty-state">
            <h2>No articles yet</h2>
            <p>Run database seeders to populate the blog.</p>
        </section>
    {else}
        <div class="category-list" data-testid="home-page">
            {foreach $categories as $category}
                <section class="category-section" data-testid="category-section">
                    <div class="category-section__header">
                        <h2>{$category.name|escape}</h2>
                        <a href="/category/{$category.slug|escape:'url'}">View All</a>
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
