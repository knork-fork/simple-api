<?php
declare(strict_types=1);

namespace App\Tests\Unit\System;

use App\System\ParameterLoader;
use App\Tests\Common\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
final class ParameterLoaderTest extends UnitTestCase
{
    /**
     * @param string[] $expectedParameters
     */
    #[DataProvider('getValidUriParameters')]
    public function testGetUriParametersReturnsParameters(string $path, string $uri, array $expectedParameters): void
    {
        $parameters = ParameterLoader::getUriParameters($path, $uri);

        self::assertSame($expectedParameters, $parameters);
    }

    /**
     * @return array<mixed>
     */
    public static function getValidUriParameters(): array
    {
        return [
            ['/test', '/test', []],
            ['/{test}', '/blabla', ['blabla']],
            ['/{test}', '/', ['']],
            ['/test/{parameter_1}', '/test/blabla', ['blabla']],
            ['/test/test2/{parameter_1}', '/test/test2/blabla', ['blabla']],
            ['/test/{parameter_1}/{parameter_2}', '/test/blabla/123', ['blabla', '123']],
            ['/test/{parameter_1}/parameter_2', '/test/123/parameter_2', ['123']],
            ['/test/{parameter}/test2', '/test/bla bla/test2', ['bla bla']],
            ['/test/{parameter}/test2', '/test/bla-bla/test2', ['bla-bla']],
        ];
    }
}
