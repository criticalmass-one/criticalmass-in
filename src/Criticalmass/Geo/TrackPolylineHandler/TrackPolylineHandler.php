<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\TrackPolylineHandler;

use App\Criticalmass\Geo\Converter\TrackToPositionListConverter;
use App\Entity\Track;
use App\Criticalmass\Geo\PolylineGenerator\PolylineGenerator;
use App\Criticalmass\Geo\PolylineGenerator\PolylineGeneratorInterface;
use App\Criticalmass\Geo\PolylineGenerator\PolylineStrategy\FullPolylineStrategy;
use App\Criticalmass\Geo\PolylineGenerator\PolylineStrategy\ReducedPolylineStrategy;
use App\Criticalmass\Geo\PositionList\PositionListInterface;

class TrackPolylineHandler implements TrackPolylineHandlerInterface
{
    /** @var PolylineGeneratorInterface $polylineGenerator */
    protected $polylineGenerator;

    /** @var TrackToPositionListConverter $trackToPositionListConverter */
    protected $trackToPositionListConverter;

    public function __construct(PolylineGenerator $polylineGenerator, TrackToPositionListConverter $trackToPositionListConverter)
    {
        $this->polylineGenerator = $polylineGenerator;
        $this->trackToPositionListConverter = $trackToPositionListConverter;
    }

    public function handleTrack(Track $track): Track
    {
        $positionList = $this->computePositionList($track);

        $track
            ->setPolyline($this->generateFullPolyline($positionList))
            ->setReducedPolyline($this->generateReducedPolyline($positionList));

        return $track;
    }

    protected function computePositionList(Track $track): PositionListInterface
    {
        return $this->trackToPositionListConverter->convert($track);
    }

    protected function generateFullPolyline(PositionListInterface $positionList): string
    {
        return $this->polylineGenerator
            ->setStrategy(new FullPolylineStrategy())
            ->execute($positionList);
    }

    protected function generateReducedPolyline(PositionListInterface $positionList): string
    {
        return $this->polylineGenerator
            ->setStrategy(new ReducedPolylineStrategy())
            ->execute($positionList);
    }
}