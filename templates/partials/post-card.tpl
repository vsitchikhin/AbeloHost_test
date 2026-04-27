<article class="post-card" data-testid="post-card" data-post-slug="{$post.slug|escape}">
    {if !empty($post.image)}
        <a class="post-card__image-link" href="/post/{$post.slug|escape:'url'}" aria-label="Read {$post.title|escape}">
            <img class="post-card__image" src="{$post.image|escape}" alt="">
        </a>
    {/if}

    <div class="post-card__body">
        <h3 class="post-card__title">
            <a href="/post/{$post.slug|escape:'url'}">{$post.title|escape}</a>
        </h3>
        <p class="post-card__description">{$post.description|escape}</p>
        <div class="post-card__meta">
            <span>{$post.published_at|date_format:'%d.%m.%Y'}</span>
            <span>{$post.views|escape} views</span>
        </div>
        <a class="post-card__read-more" href="/post/{$post.slug|escape:'url'}">Continue Reading</a>
    </div>
</article>
