<?php declare(strict_types=1);

namespace Tests\MassTrackImport\Voter;

use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;
use App\Criticalmass\MassTrackImport\Voter\LocationVoter;
use App\Entity\City;
use App\Entity\Ride;
use Caldera\GeoBasic\Coord\Coord;
use PHPUnit\Framework\TestCase;

class LocationVoterTest extends TestCase
{
    public function testBrunnen(): void
    {
        $ride = $this->createRide();

        $brunnen = new Coord(53.566908, 9.983817);

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartCoord($brunnen);

        $this->assertEquals(1.0, (new LocationVoter())->vote($ride, $stravaActivitiy));
    }

    public function testSchanzenpark(): void
    {
        $ride = $this->createRide();

        $schanzenpark = new Coord(53.565153, 9.969934);

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartCoord($schanzenpark);

        $this->assertEquals(0.9, (new LocationVoter())->vote($ride, $stravaActivitiy));
    }

    public function testWandsbek(): void
    {
        $ride = $this->createRide();

        $wandsbek = new Coord(53.5706411, 10.0621762);

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartCoord($wandsbek);

        $this->assertEquals(0.8, (new LocationVoter())->vote($ride, $stravaActivitiy));
    }

    public function testStade(): void
    {
        $ride = $this->createRide();

        $stade = new Coord(53.5967471, 9.4745388);

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartCoord($stade);

        $this->assertEquals(0.5, (new LocationVoter())->vote($ride, $stravaActivitiy));
    }

    public function testBerlin(): void
    {
        $ride = $this->createRide();

        $berlin = new Coord(52.516336, 13.378245);

        $stravaActivitiy = new StravaActivityModel();
        $stravaActivitiy->setStartCoord($berlin);

        $this->assertEquals(-1.0, (new LocationVoter())->vote($ride, $stravaActivitiy));
    }

    protected function createRide(): Ride
    {
        $city = new City();
        $city
            ->setLatitude(53.550821)
            ->setLongitude(9.993282);

        $ride = new Ride();
        $ride
            ->setCity($city)
            ->setLatitude(53.566642)
            ->setLongitude(9.984708);

        return $ride;
    }
}
