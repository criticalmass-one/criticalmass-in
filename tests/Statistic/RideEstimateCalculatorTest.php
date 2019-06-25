<?php declare(strict_types=1);

namespace Tests\Statistic;

use App\Criticalmass\Statistic\RideEstimateCalculator\RideEstimateCalculator;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use PHPUnit\Framework\TestCase;

class RideEstimateCalculatorTest extends TestCase
{
    public function testCalculatorWithoutEstimates(): void
    {
        $rideEstimateCalculator = new RideEstimateCalculator();

        $ride = new Ride();

        $ride = $rideEstimateCalculator
            ->setRide($ride)
            ->calculate()
            ->getRide();

        $this->assertEquals(0, $ride->getEstimatedDistance());
        $this->assertEquals(0, $ride->getEstimatedDuration());
        $this->assertEquals(0, $ride->getEstimatedParticipants());
    }

    public function testCalculatorWithOneDistanceEstimate(): void
    {
        $rideEstimateCalculator = new RideEstimateCalculator();

        $ride = new Ride();

        $estimateList = [
            (new RideEstimate())->setRide($ride)->setEstimatedDistance(42.3),
        ];

        $ride = $rideEstimateCalculator
            ->setRide($ride)
            ->setEstimates($estimateList)
            ->calculate()
            ->getRide();

        $this->assertEquals(42.3, $ride->getEstimatedDistance());
        $this->assertEquals(0, $ride->getEstimatedDuration());
        $this->assertEquals(0, $ride->getEstimatedParticipants());
    }

    public function testCalculatorWithOneDurationEstimate(): void
    {
        $rideEstimateCalculator = new RideEstimateCalculator();

        $ride = new Ride();

        $estimateList = [
            (new RideEstimate())->setRide($ride)->setEstimatedDuration(2.5),
        ];

        $ride = $rideEstimateCalculator
            ->setRide($ride)
            ->setEstimates($estimateList)
            ->calculate()
            ->getRide();

        $this->assertEquals(0, $ride->getEstimatedDistance());
        $this->assertEquals(2.5, $ride->getEstimatedDuration());
        $this->assertEquals(0, $ride->getEstimatedParticipants());
    }

    public function testCalculatorWithOneParticipantEstimate(): void
    {
        $rideEstimateCalculator = new RideEstimateCalculator();

        $ride = new Ride();

        $estimateList = [
            (new RideEstimate())->setRide($ride)->setEstimatedParticipants(1234),
        ];

        $ride = $rideEstimateCalculator
            ->setRide($ride)
            ->setEstimates($estimateList)
            ->calculate()
            ->getRide();

        $this->assertEquals(0, $ride->getEstimatedDistance());
        $this->assertEquals(0, $ride->getEstimatedDuration());
        $this->assertEquals(1234, $ride->getEstimatedParticipants());
    }

    public function testCalculatorWithThreeDifferentEstimates(): void
    {
        $rideEstimateCalculator = new RideEstimateCalculator();

        $ride = new Ride();

        $estimateList = [
            (new RideEstimate())->setRide($ride)->setEstimatedDistance(50.5),
            (new RideEstimate())->setRide($ride)->setEstimatedDuration(3.2),
            (new RideEstimate())->setRide($ride)->setEstimatedParticipants(4321),
        ];

        $ride = $rideEstimateCalculator
            ->setRide($ride)
            ->setEstimates($estimateList)
            ->calculate()
            ->getRide();

        $this->assertEquals(50.5, $ride->getEstimatedDistance());
        $this->assertEquals(3.2, $ride->getEstimatedDuration());
        $this->assertEquals(4321, $ride->getEstimatedParticipants());
    }

    public function testCalculatorWithTwoEstimates(): void
    {
        $rideEstimateCalculator = new RideEstimateCalculator();

        $ride = new Ride();

        $estimateList = [
            (new RideEstimate())
                ->setRide($ride)
                ->setEstimatedDistance(10.5)
                ->setEstimatedDuration(3.5)
                ->setEstimatedParticipants(500),
            (new RideEstimate())
                ->setRide($ride)
                ->setEstimatedDistance(20.5)
                ->setEstimatedDuration(4.7)
                ->setEstimatedParticipants(600),
        ];

        $ride = $rideEstimateCalculator
            ->setRide($ride)
            ->setEstimates($estimateList)
            ->calculate()
            ->getRide();

        $this->assertEquals(15.5, $ride->getEstimatedDistance());
        $this->assertEquals(4.1, $ride->getEstimatedDuration());
        $this->assertEquals(550, $ride->getEstimatedParticipants());
    }

    public function testCalculatorWithThreeEstimates(): void
    {
        $rideEstimateCalculator = new RideEstimateCalculator();

        $ride = new Ride();

        $estimateList = [
            (new RideEstimate())
                ->setRide($ride)
                ->setEstimatedDistance(10.5)
                ->setEstimatedDuration(3.5)
                ->setEstimatedParticipants(500),
            (new RideEstimate())
                ->setRide($ride)
                ->setEstimatedDistance(20.5)
                ->setEstimatedDuration(4.7)
                ->setEstimatedParticipants(600),
            (new RideEstimate())
                ->setRide($ride)
                ->setEstimatedParticipants(500),
        ];

        $ride = $rideEstimateCalculator
            ->setRide($ride)
            ->setEstimates($estimateList)
            ->calculate()
            ->getRide();

        $this->assertEquals(15.5, $ride->getEstimatedDistance());
        $this->assertEquals(4.1, $ride->getEstimatedDuration());
        $this->assertEquals(534, $ride->getEstimatedParticipants());
    }
}