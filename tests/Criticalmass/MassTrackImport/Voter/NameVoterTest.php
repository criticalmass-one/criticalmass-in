<?php declare(strict_types=1);

namespace Tests\Criticalmass\MassTrackImport\Voter;

use App\Criticalmass\MassTrackImport\Voter\NameVoter;
use App\Entity\City;
use App\Entity\Ride;
use App\Entity\TrackImportCandidate;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NameVoterTest extends TestCase
{
    /**
     * @return iterable<string, array{0: string, 1: float}>
     */
    public static function names(): iterable
    {
        yield 'identical to ride title' => ['Critical Mass Hamburg Mai 2024', 1.0];
        yield 'contains "Critical Mass"' => ['Critical Mass with friends', 0.95];
        yield 'contains both words separately' => ['Critical ride of the Mass', 0.95];
        yield 'contains only "Critical"' => ['Critical evening', 0.8];
        yield 'contains only "Mass"' => ['Massive ride', 0.8];
        yield 'mentions the city' => ['Feierabendrunde Hamburg', 0.5];
        yield 'nothing matches' => ['Morning ride', 0.0];
        yield 'lower-case is not recognised' => ['critical mass', 0.0];
        yield 'empty name' => ['', 0.0];
    }

    #[Test]
    #[DataProvider('names')]
    public function scoreReflectsHowMuchTheActivityNameLooksLikeACriticalMass(string $name, float $expected): void
    {
        $city = (new City())->setCity('Hamburg');
        $ride = (new Ride())->setCity($city)->setTitle('Critical Mass Hamburg Mai 2024');
        $candidate = (new TrackImportCandidate())->setName($name);

        self::assertSame($expected, (new NameVoter())->vote($ride, $candidate));
    }
}
