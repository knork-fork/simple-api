<?php
declare(strict_types=1);

namespace App\Tests\Fixtures;

final class DependencySorter
{
    /**
     * Dependency sorting - every time something is a dependency score is increased by 1, bigger the score the sooner it is loaded
     *
     * @param string[]               $fixtures
     * @param array<string, Fixture> $fixtureObjects
     *
     * @return Fixture[]
     */
    public static function sort(array $fixtures, array $fixtureObjects): array
    {
        $fixtureScores = [];
        foreach ($fixtures as $fixture) {
            $fixtureScores[$fixture] = 0;
        }

        foreach ($fixtures as $fixture) {
            self::rateDependenciesRecursively($fixture, $fixtureObjects, $fixtureScores);
        }

        $sortedLoadOrder = [];
        while (\count($fixtureScores) > 0) {
            $maxScore = max($fixtureScores);
            $maxScoreFixture = array_search($maxScore, $fixtureScores, true);
            $sortedLoadOrder[] = $fixtureObjects[$maxScoreFixture];
            unset($fixtureScores[$maxScoreFixture]);
        }

        return $sortedLoadOrder;
    }

    /**
     * Calculates scores recursively
     *
     * Example: if C is dependent on B and B is dependent on A, A will have a score of 2, B will have a score of 1 and C will have a score of 0.
     *
     * @param array<string, Fixture> $fixtureObjects
     * @param array<string, int>     $fixtureScores
     */
    private static function rateDependenciesRecursively(string $fixture, array $fixtureObjects, array &$fixtureScores): void
    {
        $dependencies = $fixtureObjects[$fixture]->getDependencies();
        foreach ($dependencies as $dependency) {
            ++$fixtureScores[$dependency];
            self::rateDependenciesRecursively($dependency, $fixtureObjects, $fixtureScores);
        }
    }
}
