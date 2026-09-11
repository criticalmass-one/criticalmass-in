<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\City;
use App\Repository\CityRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Wann eine Stadt als eingeschlafen gilt und nicht mehr beworben wird.
 *
 * Der Score allein reicht nicht: Eine neu angelegte Stadt hat zwangslaeufig
 * keine Signale und stuende sonst ab dem ersten Tag auf der Liste der Toten.
 */
class CityInactiveTest extends TestCase
{
    private const NOW = '2026-09-11 12:00:00';

    private function city(?float $score, ?string $createdAt): City
    {
        $city = (new City())->setActivityScore($score);

        if (null !== $createdAt) {
            $city->setCreatedAt(new \DateTime($createdAt));
        } else {
            (new \ReflectionProperty(City::class, 'createdAt'))->setValue($city, null);
        }

        return $city;
    }

    /** @return iterable<string, array{?float, ?string, bool}> */
    public static function cases(): iterable
    {
        yield 'kein Score: noch nie gemessen, bleibt sichtbar' => [null, '2020-01-01', false];
        yield 'Score am Schwellwert' => [CityRepository::ACTIVITY_SCORE_THRESHOLD, '2020-01-01', false];
        yield 'Score deutlich darueber' => [0.64, '2020-01-01', false];
        yield 'Score 0, alte Stadt' => [0.0, '2020-01-01', true];
        yield 'Score knapp unter dem Schwellwert, alte Stadt' => [0.005, '2020-01-01', true];
        yield 'Score 0, vor zwei Tagen angelegt' => [0.0, '2026-09-09 10:00:00', false];
        yield 'Score 0, vor fuenf Monaten angelegt' => [0.0, '2026-04-11 12:00:00', false];
        yield 'Score 0, genau sechs Monate alt' => [0.0, '2026-03-11 12:00:00', true];
        yield 'Score 0, ohne Anlagedatum' => [0.0, null, true];
    }

    #[DataProvider('cases')]
    public function testIsInactive(?float $score, ?string $createdAt, bool $expected): void
    {
        $city = $this->city($score, $createdAt);

        self::assertSame($expected, $city->isInactive(new \DateTimeImmutable(self::NOW)));
    }

    public function testDefaultsToTheCurrentTime(): void
    {
        self::assertFalse($this->city(0.0, 'now')->isInactive(), 'Eine eben angelegte Stadt ist nicht inaktiv.');
        self::assertTrue($this->city(0.0, '-7 months')->isInactive());
    }
}
