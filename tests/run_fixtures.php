<?php
declare(strict_types=1);

use App\Tests\Fixtures\DependencySorter;
use App\Tests\Fixtures\Fixture;

const FIXTURES_DIR = '/application/tests/Fixtures';

putenv('FIXTURE_ENV=true');
require_once '/application/src/init.php';

// Scan fixtures dir for all classes that extend Fixture and then load them in the correct order

require_once '/application/tests/Fixtures/Fixture.php';
$filenames = glob(FIXTURES_DIR . '/*.php');
$filenames = $filenames ?: [];
foreach ($filenames as $filename) {
    require_once $filename;
}

$fixtures = [];
$declaredClasses = get_declared_classes();
foreach ($declaredClasses as $className) {
    $reflector = new ReflectionClass($className);
    if ($reflector->isSubclassOf(Fixture::class)) {
        $fixtures[] = $className;
    }
}

/** @var array<string, Fixture> $fixtureObjects */
$fixtureObjects = [];
foreach ($fixtures as $fixture) {
    /** @var Fixture $fixtureObject */
    $fixtureObject = new $fixture();
    $fixtureObjects[$fixture] = $fixtureObject;
}

$fixtureLoadOrder = DependencySorter::sort($fixtures, $fixtureObjects);
foreach ($fixtureLoadOrder as $fixture) {
    echo 'Loading fixture: ' . $fixture::class . \PHP_EOL;

    try {
        $fixture->load();
    } catch (Exception $e) {
        echo 'Error loading fixture ' . $fixture::class . ': ' . $e->getMessage() . \PHP_EOL;
        echo $e->getTraceAsString() . \PHP_EOL;
    }
}
