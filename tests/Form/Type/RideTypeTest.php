<?php declare(strict_types=1);

namespace Tests\Form\Type;

use App\Entity\City;
use App\Entity\Ride;
use App\Enum\RideTypeEnum;
use App\Form\Type\RideType;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class RideTypeTest extends TypeTestCase
{
    /** @var list<string> */
    private array $roles = ['ROLE_USER'];

    protected function getExtensions(): array
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getRoleNames')->willReturnCallback(fn (): array => $this->roles);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn($token);

        return [new PreloadedExtension([new RideType($tokenStorage)], [])];
    }

    private function ride(bool $enabled = true): Ride
    {
        $city = (new City())->setCity('Hamburg')->setTimezone('Europe/Berlin');

        return (new Ride())->setCity($city)->setEnabled($enabled)->setDateTime(new \DateTime('2024-05-31 17:00:00', new \DateTimeZone('UTC')));
    }

    #[Test]
    public function regularUsersCannotEditTheSlug(): void
    {
        $form = $this->factory->create(RideType::class, $this->ride());

        self::assertFalse($form->has('slug'));
        self::assertFalse($form->has('enabled'));
        self::assertTrue($form->has('save'));
    }

    #[Test]
    public function adminsGetASlugField(): void
    {
        $this->roles = ['ROLE_USER', 'ROLE_ADMIN'];

        $form = $this->factory->create(RideType::class, $this->ride());

        self::assertTrue($form->has('slug'));
    }

    #[Test]
    public function disabledRidesOfferAnEnabledCheckbox(): void
    {
        $form = $this->factory->create(RideType::class, $this->ride(false));

        self::assertTrue($form->has('enabled'));
    }

    #[Test]
    public function dateTimeIsEnteredInCityTimezoneAndStoredInUtc(): void
    {
        $ride = $this->ride();
        $form = $this->factory->create(RideType::class, $ride);

        self::assertSame('Europe/Berlin', $form->get('dateTime')->getConfig()->getOption('view_timezone'));
        self::assertSame('UTC', $form->get('dateTime')->getConfig()->getOption('model_timezone'));

        // 17:00 UTC is 19:00 in Berlin during DST.
        self::assertSame('19:00', $form->get('dateTime')->get('time')->getViewData());

        $form->submit([
            'title' => 'Critical Mass Hamburg',
            'dateTime' => ['date' => '2024-06-28', 'time' => '19:30'],
            'rideType' => 'CRITICAL_MASS',
            'latitude' => '53.55',
            'longitude' => '9.99',
        ]);

        self::assertTrue($form->isSynchronized());
        self::assertSame('Critical Mass Hamburg', $ride->getTitle());
        self::assertSame(RideTypeEnum::CRITICAL_MASS, $ride->getRideType());
        self::assertSame('2024-06-28 17:30:00', $ride->getDateTime()->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s'));
        self::assertSame(53.55, $ride->getLatitude());
        self::assertSame(9.99, $ride->getLongitude());
    }

    #[Test]
    public function unknownRideTypeIsRejected(): void
    {
        $form = $this->factory->create(RideType::class, $this->ride());

        $form->submit(['title' => 'x', 'dateTime' => ['date' => '2024-06-28', 'time' => '19:30'], 'rideType' => 'PARTY']);

        self::assertFalse($form->get('rideType')->isSynchronized());
    }
}
