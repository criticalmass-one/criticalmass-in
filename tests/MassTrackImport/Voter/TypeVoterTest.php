<?php declare(strict_types=1);

namespace Tests\MassTrackImport\Voter;

use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;
use App\Criticalmass\MassTrackImport\Voter\TypeVoter;
use App\Entity\Ride;
use PHPUnit\Framework\TestCase;

class TypeVoterTest extends TestCase
{
    public function testRide(): void
    {
        $ride = new Ride();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setType('Ride');

        $this->assertEquals(1.0, (new TypeVoter())->vote($ride, $stravaActivitiy));
    }

    public function testRun(): void
    {
        $ride = new Ride();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setType('Run');

        $this->assertEquals(-1, (new TypeVoter())->vote($ride, $stravaActivitiy));
    }

    public function testWalk(): void
    {
        $ride = new Ride();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setType('Walk');

        $this->assertEquals(-1, (new TypeVoter())->vote($ride, $stravaActivitiy));
    }
}
