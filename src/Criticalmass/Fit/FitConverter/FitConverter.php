<?php declare(strict_types=1);

namespace App\Criticalmass\Fit\FitConverter;

use App\Criticalmass\Fit\FitParser\FitParserInterface;
use App\Criticalmass\Geo\GpxService\GpxServiceInterface;

class FitConverter implements FitConverterInterface
{
    public function __construct(
        private readonly FitParserInterface $fitParser,
        private readonly GpxServiceInterface $gpxService,
    ) {
    }

    public function convertToGpxString(string $fitFilePath): string
    {
        $fitData = $this->fitParser->parse($fitFilePath);

        $gpxFile = $this->gpxService->createGpxFromStravaStream(
            $fitData->getLatLngData(),
            $fitData->getAltitudeData(),
            $fitData->getTimeData(),
            $fitData->getStartDateTime(),
        );

        return $this->gpxService->toXmlString($gpxFile);
    }
}
