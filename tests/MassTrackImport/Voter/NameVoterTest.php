<?php declare(strict_types=1);

namespace Tests\MassTrackImport\Voter;

use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;
use App\Criticalmass\MassTrackImport\Voter\NameVoter;
use App\Entity\Ride;
use PHPUnit\Framework\TestCase;

class NameVoterTest extends TestCase
{
    public function testExactNames(): void
    {
        $ride = new Ride();
        $ride->setTitle('Critical Mass Hamburg 24. Juni 2011');

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setName('Critical Mass Hamburg 24. Juni 2011');

        $this->assertEquals(1.0, (new NameVoter())->vote($ride, $stravaActivitiy));
    }

    public function testCriticalMass(): void
    {
        $ride = new Ride();
        $ride->setTitle('Critical Mass Hamburg 24. Juni 2011');

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setName('Critical Mass Hamburg');

        $this->assertEquals(0.9, (new NameVoter())->vote($ride, $stravaActivitiy));
    }

    public function testFahrtAmAbend(): void
    {
        $ride = new Ride();
        $ride->setTitle('Critical Mass Hamburg 24. Juni 2011');

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setName('Fahrt am Abend');

        $this->assertEquals(0.0, (new NameVoter())->vote($ride, $stravaActivitiy));
    }
}