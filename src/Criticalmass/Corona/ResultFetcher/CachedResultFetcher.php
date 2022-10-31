<?php declare(strict_types=1);

namespace App\Criticalmass\Corona\ResultFetcher;

use App\Criticalmass\Corona\Model\Result;
use App\EntityInterface\CoordinateInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CachedResultFetcher extends ResultFetcher
{
    const NAMESPACE = 'cmone_corona';
    const KEY_PREFIX = 'cmone_corona_result';
    const TTL = 60*60;

    protected FilesystemAdapter $adapter;

    public function __construct(SerializerInterface $serializer)
    {
        $this->adapter = new FilesystemAdapter(self::NAMESPACE, self::TTL);

        parent::__construct($serializer);
    }

    public function fetch(CoordinateInterface $coordinate): ?Result
    {
        $key = sprintf('%s-%s-%s', self::KEY_PREFIX, $coordinate->getLatitude(), $coordinate->getLongitude());

        return $this->adapter->get($key, function() use ($coordinate) {
            return parent::fetch($coordinate);
        });
    }
}
