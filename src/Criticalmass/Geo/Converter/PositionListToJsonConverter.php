<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Converter;

use App\Criticalmass\Geo\PositionList\PositionListInterface;
use JMS\Serializer\SerializerInterface;

class PositionListToJsonConverter
{
    final const FORMAT = 'json';

    public function __construct(protected SerializerInterface $serializer)
    {
    }

    public function convert(PositionListInterface $positionList): string
    {
        return $this->serializer->serialize($positionList->getList(), self::FORMAT);
    }
}