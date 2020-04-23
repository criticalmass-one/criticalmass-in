<?php declare(strict_types=1);

namespace Tests\MassTrackImport\Voter;

use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;
use App\Criticalmass\MassTrackImport\Voter\DurationVoter;
use App\Entity\Ride;
use PHPUnit\Framework\TestCase;

class DurationVoterTest extends TestCase
{
    public function test50MinuteRide(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setElapsedTime(50 * 60);

        $this->assertEquals(0.75, (new DurationVoter())->vote($ride, $stravaActivitiy));
    }

    public function test150MinuteRide(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setElapsedTime(150 * 60);

        $this->assertEquals(0.75, (new DurationVoter())->vote($ride, $stravaActivitiy));
    }

    public function test30MinuteRide(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setElapsedTime(30 * 60);

        $this->assertEquals(0.5, (new DurationVoter())->vote($ride, $stravaActivitiy));
    }

    public function test300MinuteRide(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setElapsedTime(300 * 60);

        $this->assertEquals(0.5, (new DurationVoter())->vote($ride, $stravaActivitiy));
    }

    public function test10MinuteRide(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setElapsedTime(10 * 60);

        $this->assertEquals(0.0, (new DurationVoter())->vote($ride, $stravaActivitiy));
    }

    public function test800MinuteRide(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setElapsedTime(800 * 60);

        $this->assertEquals(0.0, (new DurationVoter())->vote($ride, $stravaActivitiy));
    }

    protected function createRide(): Ride
    {
        return new Ride();
    }
}