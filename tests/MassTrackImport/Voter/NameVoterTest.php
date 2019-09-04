<?php declare(strict_types=1);

namespace Tests\MassTrackImport\Voter;

use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;
use App\Criticalmass\MassTrackImport\Voter\NameVoter;
use App\Entity\City;
use App\Entity\Ride;
use PHPUnit\Framework\TestCase;

class NameVoterTest extends TestCase
{
    public function testExactNames(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setName('Critical Mass Hamburg 24. Juni 2011');

        $this->assertEquals(1.0, (new NameVoter())->vote($ride, $stravaActivitiy));
    }

    public function testCriticalMass(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setName('Critical Mass');

        $this->assertEquals(0.95, (new NameVoter())->vote($ride, $stravaActivitiy));
    }

    public function testCriticalMassHamburg(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setName('Critical Mass Hamburg');

        $this->assertEquals(0.95, (new NameVoter())->vote($ride, $stravaActivitiy));
    }

    public function testMassHamburgCritical(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setName('Mass Hamburg Critical');

        $this->assertEquals(0.95, (new NameVoter())->vote($ride, $stravaActivitiy));
    }

    public function testCriticalRide(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setName('Critical Ride');

        $this->assertEquals(0.8, (new NameVoter())->vote($ride, $stravaActivitiy));
    }

    public function testMassRide(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setName('Mass Ride');

        $this->assertEquals(0.8, (new NameVoter())->vote($ride, $stravaActivitiy));
    }

    public function testKritischeMasseHamburg(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setName('Kritische Masse Hamburg');

        $this->assertEquals(0.8, (new NameVoter())->vote($ride, $stravaActivitiy));
    }

    public function testKritischeTourHamburg(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setName('Kritische Tour Hamburg');

        $this->assertEquals(0.5, (new NameVoter())->vote($ride, $stravaActivitiy));
    }

    public function testRundUmHamburg(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setName('Rund um Hamburg');

        $this->assertEquals(0.5, (new NameVoter())->vote($ride, $stravaActivitiy));
    }

    public function testFahrtAmAbend(): void
    {
        $ride = $this->createRide();

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setName('Fahrt am Abend');

        $this->assertEquals(0.0, (new NameVoter())->vote($ride, $stravaActivitiy));
    }

    protected function createRide(): Ride
    {
        $city = new City();
        $city->setCity('Hamburg');

        $ride = new Ride();
        $ride
            ->setCity($city)
            ->setTitle('Critical Mass Hamburg 24. Juni 2011');

        return $ride;
    }
}