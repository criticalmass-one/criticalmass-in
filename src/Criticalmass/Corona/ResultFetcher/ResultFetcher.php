<?php declare(strict_types=1);

namespace App\Criticalmass\Corona\ResultFetcher;

use App\Criticalmass\Corona\Model\Result;
use App\EntityInterface\CoordinateInterface;
use JMS\Serializer\SerializerInterface;

class ResultFetcher implements ResultFetcherInterface
{
    protected SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function fetch(CoordinateInterface $coordinate): ?Result
    {
        $resultString = file_get_contents(sprintf('https://corona.criticalmass.in?latitude=%f&longitude=%f', $coordinate->getLatitude(), $coordinate->getLongitude()));

        $result = $this->serializer->deserialize($resultString, Result::class, 'json');

        return $result;
    }
}
