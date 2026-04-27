<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Support\Sort;
use PHPUnit\Framework\TestCase;

class SortTest extends TestCase
{
    public function testAcceptsAllowedSortValues(): void
    {
        $sort = Sort::normalize('views', 'asc');

        $this->assertSame('views', $sort['field']);
        $this->assertSame('asc', $sort['direction']);
    }

    public function testFallsBackToDefaultsForInvalidValues(): void
    {
        $sort = Sort::normalize('title', 'sideways');

        $this->assertSame('published_at', $sort['field']);
        $this->assertSame('desc', $sort['direction']);
    }
}
