<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Repositories\PostRepository;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class PostRepositoryTest extends TestCase
{
    public function testFindByCategoryFallsBackToSafeOrderByForInvalidField(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchAll')->willReturn([]);

        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('p.published_at desc'))
            ->willReturn($stmt);

        $repo = new PostRepository($pdo);
        $repo->findByCategory(1, 'malicious; DROP TABLE posts;--', 'sideways', 10, 0);
    }

    public function testFindByCategoryFallsBackToSafeDirectionForInvalidDirection(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchAll')->willReturn([]);

        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('p.views desc'))
            ->willReturn($stmt);

        $repo = new PostRepository($pdo);
        $repo->findByCategory(1, 'views', 'sideways', 10, 0);
    }

    public function testFindByCategoryPassesThroughValidSortParams(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchAll')->willReturn([]);

        $pdo = $this->createMock(PDO::class);
        $pdo->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('p.views asc'))
            ->willReturn($stmt);

        $repo = new PostRepository($pdo);
        $repo->findByCategory(1, 'views', 'asc', 10, 0);
    }
}
