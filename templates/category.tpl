{extends file='layouts/app.tpl'}

{block name='title'}{$category.name|escape} - {$appName|escape}{/block}

{block name='content'}
    <section class="listing-heading" data-testid="category-page">
        <div>
            <p class="page-heading__eyebrow">Category</p>
            <h1>{$category.name|escape}</h1>
            <p class="listing-heading__description">{$category.description|escape}</p>
        </div>
        <a class="back-link" href="/">Back to home</a>
    </section>

    <nav class="toolbar" aria-label="Article sorting">
        <span>Sort by</span>
        <a class="{if $sortBy == 'published_at'}is-active{/if}"
           href="/category/{$category.slug|escape:'url'}?sort=published_at&dir=desc">Newest</a>
        <a class="{if $sortBy == 'views'}is-active{/if}"
           href="/category/{$category.slug|escape:'url'}?sort=views&dir=desc">Popular</a>
    </nav>

    {if empty($posts)}
        <section class="empty-state" data-testid="empty-state">
            <h2>No articles in this category yet</h2>
        </section>
    {else}
        <section class="post-grid" data-testid="category-posts">
            {foreach $posts as $post}
                {include file='partials/post-card.tpl' post=$post}
            {/foreach}
        </section>
    {/if}

    {if $totalPages > 1}
        <nav class="pagination" aria-label="Pagination">
            {for $pageNumber=1 to $totalPages}
                <a class="pagination__link{if $pageNumber == $page} pagination__link--active{/if}"
                   href="/category/{$category.slug|escape:'url'}?sort={$sortBy|escape:'url'}&dir={$direction|escape:'url'}&page={$pageNumber}">
                    {$pageNumber}
                </a>
            {/for}
        </nav>
    {/if}
{/block}
