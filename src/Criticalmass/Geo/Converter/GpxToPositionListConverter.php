<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Converter;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\EntityInterface\PositionInterface;
use App\Criticalmass\Geo\GpxReader\GpxReaderInterface;
use App\Criticalmass\Geo\PositionList\PositionList;

class GpxToPositionListConverter
{
    /** @var GpxReaderInterface $gpxReader */
    protected $gpxReader;

    public function __construct(GpxReaderInterface $gpxReader)
    {
        $this->gpxReader = $gpxReader;
    }

    public function convert(string $filename): ?PositionList
    {
        $this->gpxReader->loadFromFile($filename);

        $positionList = new PositionList();

        for ($n = 0; $n < $this->gpxReader->countPoints(); ++$n) {
            $point = $this->gpxReader->getPoint($n);

            $position = GpxPointToPositionConverter::convert($point);

            if ($position) {
                $positionList->add($position);
            }
        }

        return $positionList;
    }
}