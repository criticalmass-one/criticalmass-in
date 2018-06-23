<?php declare(strict_types=1);

namespace Criticalmass\Component\Gps\DistanceCalculator;

use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Component\Gps\GpxReader\TrackReader;
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
