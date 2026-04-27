<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Controllers\DocsController;
use PHPUnit\Framework\TestCase;

class DocsControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        http_response_code(200);
    }

    public function testOutputsOpenApiSpecification(): void
    {
        $controller = new DocsController();

        $this->expectOutputRegex('/openapi: 3\.0\.3/');

        $controller->openapi();
    }

    public function testReturnsNotFoundForUnknownAsset(): void
    {
        $controller = new DocsController();

        $this->expectOutputString('');

        $controller->asset('../package.json');

        $this->assertSame(404, http_response_code());
    }
}
