<?php declare(strict_types=1);

namespace Tests\Controller\Api;

use App\Criticalmass\Util\ClassUtil;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Controller\Api\Util\IdKiller;

abstract class AbstractApiControllerTest extends WebTestCase
{
    /**
     * @deprecated
     */
    protected function assertIdLessJsonEquals(string $expected, string $actual): void
    {
        $this->assertEquals(IdKiller::removeIds($expected), IdKiller::removeIds($actual));
    }

    protected function getSerializer(): SerializerInterface
    {
        return static::$container->get('jms_serializer');
    }

    protected function deserializeEntityList(string $data, string $entityFqcn): array
    {
        $type = sprintf('array<%s>', $entityFqcn);

        return $this->getSerializer()->deserialize($data, $type, 'json');
    }

    protected function deserializeEntity(string $data, string $entityFqcn): object
    {
        return $this->getSerializer()->deserialize($data, $entityFqcn, 'json');
    }

    protected function getApiEndpointForFqcn(string $fqcn): string
    {
        return sprintf('/api/%s', ClassUtil::getLowercaseShortnameFromFqcn($fqcn));
    }
}
