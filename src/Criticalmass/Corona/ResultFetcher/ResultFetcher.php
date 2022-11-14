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
        try {
            $resultString = file_get_contents(sprintf('https://corona.criticalmass.in?latitude=%f&longitude=%f', $coordinate->getLatitude(), $coordinate->getLongitude()));

            if ($resultString) {
                return $this->serializer->deserialize($resultString, Result::class, 'json');
            }
        } catch (\Exception $exception) {
            return null;
        }

        return null;
    }
}
