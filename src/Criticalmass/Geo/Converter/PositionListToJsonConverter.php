<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Converter;

use App\Criticalmass\Geo\PositionList\PositionListInterface;
use JMS\Serializer\SerializerInterface;

class PositionListToJsonConverter
{
    const FORMAT = 'json';

    /** @var SerializerInterface $serializer */
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function convert(PositionListInterface $positionList): string
    {
        return $this->serializer->serialize($positionList->getList(), self::FORMAT);
    }
}