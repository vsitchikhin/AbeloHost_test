<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\NotFoundException;
use App\Core\Paginator;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Support\Sort;

class BlogService
{
    private const POSTS_PER_PAGE = 9;

    public function __construct(
        private CategoryRepository $categories,
        private PostRepository $posts
    ) {
    }

    /**
     * @return array{categories: list<array<string, mixed>>}
     */
    public function getHomePageData(): array
    {
        return [
            'categories' => $this->categories->findWithLatestPosts(3),
        ];
    }

    /**
     * @return array{
     *     category: array<string, mixed>,
     *     posts: list<array<string, mixed>>,
     *     page: int,
     *     totalPages: int,
     *     total: int,
     *     sortBy: string,
     *     direction: string
     * }
     */
    public function getCategoryPageData(string $slug, string $sortBy, string $direction, int $page): array
    {
        $category = $this->categories->findBySlug($slug);

        if ($category === null) {
            throw new NotFoundException('Category not found.');
        }

        return $this->buildCategoryPageData($category, $sortBy, $direction, $page);
    }

    /**
     * @return array{
     *     category: array<string, mixed>,
     *     posts: list<array<string, mixed>>,
     *     page: int,
     *     totalPages: int,
     *     total: int,
     *     sortBy: string,
     *     direction: string
     * }
     */
    public function getCategoryPageDataById(int $id, string $sortBy, string $direction, int $page): array
    {
        $category = $this->categories->findById($id);

        if ($category === null) {
            throw new NotFoundException('Category not found.');
        }

        return $this->buildCategoryPageData($category, $sortBy, $direction, $page);
    }

    /**
     * @return array{post: array<string, mixed>, similar: list<array<string, mixed>>}
     */
    public function getPostPageData(string $slug): array
    {
        $post = $this->posts->findBySlugWithCategories($slug);

        if ($post === null) {
            throw new NotFoundException('Post not found.');
        }

        return $this->buildPostPageData($post);
    }

    /**
     * @return array{post: array<string, mixed>, similar: list<array<string, mixed>>}
     */
    public function getPostPageDataById(int $id): array
    {
        $post = $this->posts->findByIdWithCategories($id);

        if ($post === null) {
            throw new NotFoundException('Post not found.');
        }

        return $this->buildPostPageData($post);
    }

    /**
     * @param array<string, mixed> $category
     * @return array{
     *     category: array<string, mixed>,
     *     posts: list<array<string, mixed>>,
     *     page: int,
     *     totalPages: int,
     *     total: int,
     *     sortBy: string,
     *     direction: string
     * }
     */
    private function buildCategoryPageData(array $category, string $sortBy, string $direction, int $page): array
    {
        $sort       = Sort::normalize($sortBy, $direction);
        $total      = $this->posts->countByCategory((int) $category['id']);
        $pagination = Paginator::make($total, $page, self::POSTS_PER_PAGE);
        $posts      = $this->posts->findByCategory(
            (int) $category['id'],
            $sort['field'],
            $sort['direction'],
            $pagination['perPage'],
            $pagination['offset']
        );

        return [
            'category'   => $category,
            'posts'      => $posts,
            'page'       => $pagination['currentPage'],
            'totalPages' => $pagination['totalPages'],
            'total'      => $pagination['total'],
            'sortBy'     => $sort['field'],
            'direction'  => $sort['direction'],
        ];
    }

    /**
     * @param array<string, mixed> $post
     * @return array{post: array<string, mixed>, similar: list<array<string, mixed>>}
     */
    private function buildPostPageData(array $post): array
    {
        $postId = (int) $post['id'];
        $this->posts->incrementViews($postId);

        /** @var list<array<string, mixed>> $postCategories */
        $postCategories = (array) $post['categories'];

        /** @var list<int> $categoryIds */
        $categoryIds = array_map(
            static fn (mixed $category): int => (int) (is_array($category) ? $category['id'] : 0),
            $postCategories
        );

        return [
            'post'    => $post,
            'similar' => $this->posts->findSimilar($postId, $categoryIds, 3),
        ];
    }
}
