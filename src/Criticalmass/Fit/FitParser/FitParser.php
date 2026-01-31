<?php declare(strict_types=1);

namespace App\Criticalmass\Fit\FitParser;

use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\InvalidFitFileException;

class FitParser implements FitParserInterface
{
    public function parse(string $filePath): FitData
    {
        try {
            $pFFA = new \adriangibbons\phpFITFileAnalysis($filePath, ['units' => 'metric']);
        } catch (\Exception $e) {
            throw new InvalidFitFileException(sprintf('Could not parse FIT file: %s', $e->getMessage()));
        }

        $records = $pFFA->data_mesgs['record'] ?? [];

        $timestamps = $records['timestamp'] ?? [];
        $latitudes = $records['position_lat'] ?? [];
        $longitudes = $records['position_long'] ?? [];
        $altitudes = $records['altitude'] ?? [];

        if (empty($timestamps) || empty($latitudes) || empty($longitudes)) {
            throw new InvalidFitFileException('FIT file contains no valid GPS coordinates.');
        }

        $latLngData = [];
        $altitudeData = [];
        $timeData = [];
        $firstTimestamp = null;

        foreach ($timestamps as $ts) {
            $lat = $latitudes[$ts] ?? null;
            $lng = $longitudes[$ts] ?? null;

            if ($lat === null || $lng === null || ($lat == 0 && $lng == 0)) {
                continue;
            }

            $lat = (float) $lat;
            $lng = (float) $lng;

            if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
                continue;
            }

            if ($firstTimestamp === null) {
                $firstTimestamp = $ts;
            }

            $latLngData[] = [$lat, $lng];
            $altitudeData[] = (float) ($altitudes[$ts] ?? 0);
            $timeData[] = (int) ($ts - $firstTimestamp);
        }

        if (empty($latLngData)) {
            throw new InvalidFitFileException('FIT file contains no valid GPS coordinates after filtering.');
        }

        $startDateTime = new \DateTime(sprintf('@%d', $firstTimestamp));

        return new FitData($latLngData, $altitudeData, $timeData, $startDateTime);
    }
}
