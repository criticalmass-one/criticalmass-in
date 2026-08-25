<?php declare(strict_types=1);

namespace Tests\Form\Type;

use App\Enum\RideDisabledReasonEnum;
use App\Form\Type\LoginType;
use App\Form\Type\PhotoCoordType;
use App\Form\Type\PostType;
use App\Form\Type\ProfileColorType;
use App\Form\Type\RideDisableType;
use App\Form\Type\RideEstimateType;
use App\Form\Type\TrackRangeType;
use App\Form\Type\UsernameType;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Test\TypeTestCase;

final class SimpleFormTypesTest extends TypeTestCase
{
    #[Test]
    public function loginFormAsksForEmailAndSubmit(): void
    {
        $form = $this->factory->create(LoginType::class);

        self::assertInstanceOf(EmailType::class, $form->get('email')->getConfig()->getType()->getInnerType());
        self::assertInstanceOf(SubmitType::class, $form->get('submit')->getConfig()->getType()->getInnerType());
        self::assertSame('E-Mail-Adresse', $form->get('email')->getConfig()->getOption('label'));

        $form->submit(['email' => 'malte@example.org']);

        self::assertTrue($form->isSynchronized());
        self::assertSame('malte@example.org', $form->getData()['email']);
    }

    #[Test]
    public function postMessageIsOptional(): void
    {
        $form = $this->factory->create(PostType::class);

        self::assertFalse($form->get('message')->isRequired());

        $form->submit(['message' => '']);

        self::assertTrue($form->isSynchronized());
        self::assertNull($form->getData()['message']);
    }

    #[Test]
    public function rideEstimateRequiresParticipants(): void
    {
        $form = $this->factory->create(RideEstimateType::class);

        self::assertTrue($form->get('estimatedParticipants')->isRequired());

        $form->submit(['estimatedParticipants' => '250']);

        self::assertSame('250', $form->getData()['estimatedParticipants']);
    }

    #[Test]
    public function rideDisableMapsReasonToEnum(): void
    {
        $form = $this->factory->create(RideDisableType::class);

        self::assertTrue($form->get('disabledReason')->getConfig()->getOption('expanded'));
        self::assertFalse($form->get('disabledReasonMessage')->isRequired());

        $form->submit(['disabledReason' => 'CANCELLED_WEATHER', 'disabledReasonMessage' => 'Sturm']);

        self::assertTrue($form->isSynchronized());
        self::assertSame(RideDisabledReasonEnum::CANCELLED_WEATHER, $form->getData()['disabledReason']);
        self::assertSame('Sturm', $form->getData()['disabledReasonMessage']);
    }

    #[Test]
    public function rideDisableRejectsUnknownReason(): void
    {
        $form = $this->factory->create(RideDisableType::class);

        $form->submit(['disabledReason' => 'NOT_A_REASON']);

        self::assertFalse($form->get('disabledReason')->isSynchronized());
    }

    #[Test]
    public function trackRangeConsistsOfHiddenFieldsOnly(): void
    {
        $form = $this->factory->create(TrackRangeType::class);

        foreach (['startPoint', 'endPoint', 'points', 'polyline', 'reducedPolyline'] as $field) {
            self::assertInstanceOf(HiddenType::class, $form->get($field)->getConfig()->getType()->getInnerType(), $field);
        }

        $form->submit(['startPoint' => '10', 'endPoint' => '200', 'points' => '250', 'polyline' => 'abc', 'reducedPolyline' => 'ab']);

        self::assertSame(['startPoint' => '10', 'endPoint' => '200', 'points' => '250', 'polyline' => 'abc', 'reducedPolyline' => 'ab'], $form->getData());
    }

    #[Test]
    public function photoCoordinatesAreHiddenLatitudeAndLongitude(): void
    {
        $form = $this->factory->create(PhotoCoordType::class);

        self::assertInstanceOf(HiddenType::class, $form->get('latitude')->getConfig()->getType()->getInnerType());
        self::assertInstanceOf(HiddenType::class, $form->get('longitude')->getConfig()->getType()->getInnerType());

        $form->submit(['latitude' => '53.55', 'longitude' => '9.99']);

        self::assertSame(['latitude' => '53.55', 'longitude' => '9.99'], $form->getData());
    }

    #[Test]
    public function profileColorAcceptsHexColors(): void
    {
        $form = $this->factory->create(ProfileColorType::class);

        self::assertSame('Profilfarbe wählen', $form->get('color')->getConfig()->getOption('label'));

        $form->submit(['color' => '#ff8800']);

        self::assertTrue($form->isSynchronized());
        self::assertSame('#ff8800', $form->getData()['color']);
    }

    #[Test]
    public function usernameFormHasASingleTextField(): void
    {
        $form = $this->factory->create(UsernameType::class);

        self::assertSame(['username'], array_keys($form->all()));

        $form->submit(['username' => 'malte']);

        self::assertSame('malte', $form->getData()['username']);
    }
}
