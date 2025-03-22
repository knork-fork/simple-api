<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Tests\Common\UnitTestCase;

/**
 * @internal
 */
final class ExampleUnitTest extends UnitTestCase
{
    public function testPhpunitWorks(): void
    {
        $str = 'hello world';

        self::assertSame('hello world', $str);
    }
}
