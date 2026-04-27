<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Core\Paginator;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    public function testBuildsPaginationMetadata(): void
    {
        $pagination = Paginator::make(25, 2, 10);

        $this->assertSame(2, $pagination['currentPage']);
        $this->assertSame(10, $pagination['perPage']);
        $this->assertSame(25, $pagination['total']);
        $this->assertSame(3, $pagination['totalPages']);
        $this->assertSame(10, $pagination['offset']);
    }

    public function testClampsInvalidPageValues(): void
    {
        $pagination = Paginator::make(5, 99, 10);

        $this->assertSame(1, $pagination['currentPage']);
        $this->assertSame(1, $pagination['totalPages']);
        $this->assertSame(0, $pagination['offset']);
    }
}
