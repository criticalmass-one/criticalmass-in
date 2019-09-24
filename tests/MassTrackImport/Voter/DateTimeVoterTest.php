<?php declare(strict_types=1);

namespace Tests\MassTrackImport\Voter;

use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;
use App\Criticalmass\MassTrackImport\Voter\DateTimeVoter;
use App\Entity\Ride;
use PHPUnit\Framework\TestCase;

class DateTimeVoterTest extends TestCase
{
    public function testExactDateTime(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 19:00:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(1.0, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function test5MinutesBeforeDiff(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 18:55:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(1.0, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function test5MinutesPastDiff(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 19:05:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(1.0, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function test15MinutesBeforeDiff(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 18:45:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(1.0, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function test15MinutesPastDiff(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 19:15:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(1.0, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function test30MinutesBeforeDiff(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 18:30:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(0.9, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function test30MinutesPastDiff(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 19:30:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(0.9, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function test45MinutesBeforeDiff(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 18:15:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(0.8, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function test45MinutesPastDiff(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 19:45:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(0.8, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function test90MinutesBeforeDiff(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 17:30:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(0.7, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function test90MinutesPastDiff(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 20:30:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(0.7, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function test180MinutesBeforeDiff(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 16:00:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(0.5, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function test180MinutesPastDiff(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 22:00:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(0.5, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function test240MinutesBeforeDiff(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 15:00:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(0.3, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function test240MinutesPastDiff(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 23:00:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(0.3, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function testAtLeastSameDay(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-24 05:00:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(0.25, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function testYesterdayDateTime(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-23 19:00:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(-1.0, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    public function testTomorrowDateTime(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartDateTime(new \DateTime('2011-06-25 19:00:00', new \DateTimeZone('Europe/Berlin')));

        $this->assertEquals(-1.0, (new DateTimeVoter())->vote($ride, $stravaActivitiy));
    }

    protected function createRide(): Ride
    {
        $ride = new Ride();
        $ride->setDateTime(new \DateTime('2011-06-24 19:00:00', new \DateTimeZone('Europe/Berlin')));

        return $ride;
    }
}
