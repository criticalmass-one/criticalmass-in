<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Converter;

use App\Criticalmass\Geo\PositionList\PositionListInterface;
use Symfony\Component\Serializer\SerializerInterface;

/** @deprecated  */
class PositionListToJsonConverter
{
    private const string FORMAT = 'json';

    public function __construct(private readonly SerializerInterface $serializer)
    {

    }

    public function convert(PositionListInterface $positionList): string
    {
        return $this->serializer->serialize($positionList->getList(), self::FORMAT);
    }
}