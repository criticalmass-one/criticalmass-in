<?php declare(strict_types=1);

namespace Tests\Form\Type;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Form\Type\CityCycleType;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\TypeTestCase;

final class CityCycleTypeTest extends TypeTestCase
{
    private function cycle(?string $calculatorFqcn = null): CityCycle
    {
        $city = (new City())->setCity('Hamburg')->setTimezone('Europe/Berlin');

        return (new CityCycle())->setCity($city)->setRideCalculatorFqcn($calculatorFqcn);
    }

    #[Test]
    public function regularCyclesChooseDayAndWeek(): void
    {
        $cycle = $this->cycle();
        $form = $this->factory->create(CityCycleType::class, $cycle);

        self::assertInstanceOf(ChoiceType::class, $form->get('dayOfWeek')->getConfig()->getType()->getInnerType());
        self::assertSame(['Montag' => 1, 'Dienstag' => 2, 'Mittwoch' => 3, 'Donnerstag' => 4, 'Freitag' => 5, 'Sonnabend' => 6, 'Sonntag' => 0], $form->get('dayOfWeek')->getConfig()->getOption('choices'));
        self::assertSame(0, $form->get('weekOfMonth')->getConfig()->getOption('choices')['Letzte Woche im Monat']);

        $form->submit([
            'dayOfWeek' => '5',
            'weekOfMonth' => '0',
            'location' => 'Rathausmarkt',
            'time' => '17:00',
            'validFrom' => '2024-01-01',
        ]);

        self::assertTrue($form->isSynchronized());
        self::assertSame(5, $cycle->getDayOfWeek());
        self::assertSame(0, $cycle->getWeekOfMonth());
        self::assertSame('Rathausmarkt', $cycle->getLocation());
        self::assertSame('17:00', $cycle->getTime()->format('H:i'));
        self::assertSame('2024-01-01', $cycle->getValidFrom()->format('Y-m-d'));
        self::assertNull($cycle->getValidUntil());
    }

    #[Test]
    public function calculatedCyclesShowReadOnlyDayAndWeek(): void
    {
        $cycle = $this->cycle('App\\Criticalmass\\RideGenerator\\SomeCalculator')
            ->setSpecialDayOfWeek('third thursday')
            ->setSpecialWeekOfMonth('n/a');
        $form = $this->factory->create(CityCycleType::class, $cycle);

        self::assertInstanceOf(TextType::class, $form->get('dayOfWeek')->getConfig()->getType()->getInnerType());
        self::assertTrue($form->get('dayOfWeek')->isDisabled());
        self::assertTrue($form->get('weekOfMonth')->isDisabled());
        self::assertSame('third thursday', $form->get('dayOfWeek')->getData());
        self::assertSame('n/a', $form->get('weekOfMonth')->getData());
    }
}
