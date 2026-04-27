<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Core\NotFoundException;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Services\BlogService;
use PHPUnit\Framework\TestCase;

class BlogServiceTest extends TestCase
{
    public function testBuildsHomePageData(): void
    {
        $categories = [
            [
                'id'          => 1,
                'name'        => 'PHP',
                'slug'        => 'php',
                'description' => 'PHP posts',
                'posts'       => [],
            ],
        ];

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $postRepository     = $this->createMock(PostRepository::class);

        $categoryRepository
            ->expects($this->once())
            ->method('findWithLatestPosts')
            ->with(3)
            ->willReturn($categories);

        $service = new BlogService($categoryRepository, $postRepository);

        $this->assertSame(['categories' => $categories], $service->getHomePageData());
    }

    public function testBuildsCategoryPageDataWithSortingAndPagination(): void
    {
        $category = [
            'id'          => 7,
            'name'        => 'Backend',
            'slug'        => 'backend',
            'description' => 'Backend posts',
        ];
        $posts = [
            [
                'id'           => 15,
                'title'        => 'Most viewed post',
                'slug'         => 'most-viewed-post',
                'description'  => 'Description',
                'image'        => '/image.jpg',
                'views'        => 100,
                'published_at' => '2026-04-20 10:00:00',
            ],
        ];

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $postRepository     = $this->createMock(PostRepository::class);

        $categoryRepository
            ->expects($this->once())
            ->method('findBySlug')
            ->with('backend')
            ->willReturn($category);

        $postRepository
            ->expects($this->once())
            ->method('countByCategory')
            ->with(7)
            ->willReturn(12);

        $postRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->with(7, 'views', 'asc', 9, 9)
            ->willReturn($posts);

        $service = new BlogService($categoryRepository, $postRepository);
        $data    = $service->getCategoryPageData('backend', 'views', 'asc', 2);

        $this->assertSame($category, $data['category']);
        $this->assertSame($posts, $data['posts']);
        $this->assertSame(2, $data['page']);
        $this->assertSame(2, $data['totalPages']);
        $this->assertSame(12, $data['total']);
        $this->assertSame('views', $data['sortBy']);
        $this->assertSame('asc', $data['direction']);
    }

    public function testCategoryPageDataFallsBackToSafeSortDefaults(): void
    {
        $category = [
            'id'          => 3,
            'name'        => 'PHP',
            'slug'        => 'php',
            'description' => 'PHP posts',
        ];

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $postRepository     = $this->createMock(PostRepository::class);

        $categoryRepository
            ->method('findBySlug')
            ->willReturn($category);

        $postRepository
            ->method('countByCategory')
            ->willReturn(1);

        $postRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->with(3, 'published_at', 'desc', 9, 0)
            ->willReturn([]);

        $service = new BlogService($categoryRepository, $postRepository);
        $data    = $service->getCategoryPageData('php', 'title', 'sideways', 0);

        $this->assertSame('published_at', $data['sortBy']);
        $this->assertSame('desc', $data['direction']);
        $this->assertSame(1, $data['page']);
    }

    public function testThrowsWhenCategoryDoesNotExist(): void
    {
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $postRepository     = $this->createMock(PostRepository::class);

        $categoryRepository
            ->method('findBySlug')
            ->with('missing')
            ->willReturn(null);

        $postRepository
            ->expects($this->never())
            ->method('findByCategory');

        $service = new BlogService($categoryRepository, $postRepository);

        $this->expectException(NotFoundException::class);

        $service->getCategoryPageData('missing', 'published_at', 'desc', 1);
    }

    public function testBuildsPostPageDataAndIncrementsViews(): void
    {
        $post = [
            'id'           => 10,
            'title'        => 'Post title',
            'slug'         => 'post-title',
            'description'  => 'Description',
            'content'      => 'Content',
            'image'        => '/post.jpg',
            'views'        => 5,
            'published_at' => '2026-04-20 10:00:00',
            'categories'   => [
                ['id' => 1, 'name' => 'PHP', 'slug' => 'php'],
                ['id' => 2, 'name' => 'MySQL', 'slug' => 'mysql'],
            ],
        ];
        $similar = [
            [
                'id'           => 11,
                'title'        => 'Similar post',
                'slug'         => 'similar-post',
                'description'  => 'Description',
                'image'        => '/similar.jpg',
                'views'        => 3,
                'published_at' => '2026-04-21 10:00:00',
            ],
        ];

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $postRepository     = $this->createMock(PostRepository::class);

        $postRepository
            ->expects($this->once())
            ->method('findBySlugWithCategories')
            ->with('post-title')
            ->willReturn($post);

        $postRepository
            ->expects($this->once())
            ->method('incrementViews')
            ->with(10);

        $postRepository
            ->expects($this->once())
            ->method('findSimilar')
            ->with(10, [1, 2], 3)
            ->willReturn($similar);

        $service = new BlogService($categoryRepository, $postRepository);
        $data    = $service->getPostPageData('post-title');

        $this->assertSame($post, $data['post']);
        $this->assertSame($similar, $data['similar']);
    }

    public function testThrowsWhenPostDoesNotExist(): void
    {
        $categoryRepository = $this->createMock(CategoryRepository::class);
        $postRepository     = $this->createMock(PostRepository::class);

        $postRepository
            ->method('findBySlugWithCategories')
            ->with('missing')
            ->willReturn(null);

        $postRepository
            ->expects($this->never())
            ->method('incrementViews');

        $service = new BlogService($categoryRepository, $postRepository);

        $this->expectException(NotFoundException::class);

        $service->getPostPageData('missing');
    }
}
