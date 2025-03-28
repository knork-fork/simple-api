<?php
declare(strict_types=1);

namespace App\Tests\Fixtures;

abstract class Fixture
{
    abstract public function load(): void;

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [];
    }
}
