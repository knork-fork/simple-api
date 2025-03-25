<?php
declare(strict_types=1);

namespace App\Tests\Unit\System;

use App\System\PathMatcher;
use App\Tests\Common\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
final class PathMatcherTest extends UnitTestCase
{
    #[DataProvider('getMatchingPaths')]
    public function testDoesPathMatchForMatchReturnsTrue(string $path, string $uri): void
    {
        self::assertTrue(PathMatcher::doesPathMatch($path, $uri));
    }

    /**
     * @return array<mixed>
     */
    public static function getMatchingPaths(): array
    {
        return [
            ['/test', '/test'],
            ['/{test}', '/blabla'],
            ['/{test}', '/'],
            ['/test/{parameter_1}', '/test/blabla'],
            ['/test/test2/{parameter_1}', '/test/test2/blabla'],
            ['/test/{parameter_1}/{parameter_2}', '/test/blabla/123'],
            ['/test/{parameter_1}/parameter_2', '/test/123/parameter_2'],
        ];
    }

    #[DataProvider('getNonmatchingPaths')]
    public function testDoesPathMatchForNoMatchReturnsFalse(string $path, string $uri): void
    {
        self::assertFalse(PathMatcher::doesPathMatch($path, $uri));
    }

    /**
     * @return array<mixed>
     */
    public static function getNonmatchingPaths(): array
    {
        return [
            ['/test', '/test2'],
            ['/test', '/test/'],
            ['/test', '/test/123'],
            ['/test/test2', '/test/blabla'],
            ['/test/{parameter_1}/parameter_2', '/test/123/blabla'],
            ['/test/{parameter_1}/{parameter_2}', '/test/blabla'],
            ['/test/{parameter_1}/{parameter_2}', '/test/'],
            ['/test/{parameter_1}/{parameter_2}', '/test'],
        ];
    }
}
