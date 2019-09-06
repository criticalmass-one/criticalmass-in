<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\TrackDecider;

use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;
use App\Criticalmass\MassTrackImport\Voter\VoterInterface;
use App\Entity\Ride;

class RideResult
{
    /** @var Ride $ride */
    protected $ride;

    /** @var StravaActivityModel $activity */
    protected $activity;

    /** @var float $result */
    protected $result;

    /** @var array $voterResults */
    protected $voterResults;

    public function __construct(Ride $ride, StravaActivityModel $activity)
    {
        $this->ride = $ride;
        $this->activity = $activity;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function getActivity(): StravaActivityModel
    {
        return $this->activity;
    }
    
    public function getResult(): float
    {
        return $this->result;
    }

    public function setResult(float $result): RideResult
    {
        $this->result = $result;

        return $this;
    }

    public function getVoterResults(): array
    {
        return $this->voterResults;
    }

    public function addVoterResult(VoterInterface $voter, float $result): RideResult
    {
        $shortname = get_class($voter);

        $this->voterResults[$shortname] = $result;

        return $this;
    }


}
