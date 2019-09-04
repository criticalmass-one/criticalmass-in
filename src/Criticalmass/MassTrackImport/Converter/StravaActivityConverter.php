<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Converter;

use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;

class StravaActivityConverter
{
    private function __construct()
    {

    }

    public static function convert(array $content): StravaActivityModel
    {
        $model = new StravaActivityModel();
        $model
            ->setId($content['id'])
            ->setName($content['name'])
            ->setDistance($content['distance'])
            ->setElapsedTime($content['elapsed_time'])
            ->setStartDate(new \DateTime($content['start_date']))
            ->setType($content['type']);

        return $model;
    }
}