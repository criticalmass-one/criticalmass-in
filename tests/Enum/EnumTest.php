<?php declare(strict_types=1);

namespace Tests\Enum;

use App\Enum\PolylineResolution;
use App\Enum\RideDisabledReasonEnum;
use App\Enum\RideTypeEnum;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EnumTest extends TestCase
{
    #[Test]
    public function rideTypeChoicesMapValuesToLabels(): void
    {
        $choices = RideTypeEnum::choices();

        self::assertCount(count(RideTypeEnum::cases()), $choices);
        self::assertSame('Critical Mass', $choices['CRITICAL_MASS']);
        self::assertSame('Kidical Mass', $choices['KIDICAL_MASS']);

        foreach (RideTypeEnum::cases() as $case) {
            self::assertSame($case->label(), $choices[$case->value]);
        }
    }

    #[Test]
    public function disabledReasonChoicesMapValuesToLabels(): void
    {
        $choices = RideDisabledReasonEnum::choices();

        self::assertCount(count(RideDisabledReasonEnum::cases()), $choices);
        self::assertSame('diese Tour wurde abgesagt', $choices['CANCELLED']);

        foreach (RideDisabledReasonEnum::cases() as $case) {
            self::assertNotSame('', $case->label());
        }
    }

    #[Test]
    public function polylineResolutionsGetFinerWithSmallerTolerance(): void
    {
        self::assertGreaterThan(PolylineResolution::MEDIUM->tolerance(), PolylineResolution::COARSE->tolerance());
        self::assertGreaterThan(PolylineResolution::FINE->tolerance(), PolylineResolution::MEDIUM->tolerance());

        self::assertSame('100 m', PolylineResolution::COARSE->label());
        self::assertSame('10 m', PolylineResolution::MEDIUM->label());
        self::assertSame('2 m', PolylineResolution::FINE->label());
    }

    #[Test]
    public function polylineResolutionValuesMatchTheirLabelsInMetres(): void
    {
        foreach (PolylineResolution::cases() as $case) {
            self::assertSame(sprintf('%d m', $case->value), $case->label());
        }
    }
}
