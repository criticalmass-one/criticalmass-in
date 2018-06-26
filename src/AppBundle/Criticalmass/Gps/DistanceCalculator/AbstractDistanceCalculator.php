<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Gps\DistanceCalculator;

use AppBundle\Criticalmass\Gps\GpxReader\TrackReader;
use AppBundle\Entity\Track;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @deprecated
 */
abstract class AbstractDistanceCalculator implements TrackDistanceCalculatorInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var TrackReader $trackReader */
    protected $trackReader;

    /** @var Track $track */
    protected $track;

    public function __construct(RegistryInterface $registry, TrackReader $trackReader)
    {
        $this->registry = $registry;

        $this->trackReader = $trackReader;
    }

    public function loadTrack(Track $track): TrackDistanceCalculatorInterface
    {
        $this->track = $track;
        $this->trackReader->loadTrack($track);

        return $this;
    }
}
