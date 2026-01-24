<?php declare(strict_types=1);

namespace Tests\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractApiControllerTestCase extends WebTestCase
{
    protected ?KernelBrowser $client = null;
    protected ?EntityManagerInterface $entityManager = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    protected function getSerializer(): SerializerInterface
    {
        return static::getContainer()->get('jms_serializer');
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

    protected function getJsonResponse(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true);
    }

    protected function assertResponseStatusCode(int $expectedStatusCode): void
    {
        $this->assertEquals(
            $expectedStatusCode,
            $this->client->getResponse()->getStatusCode(),
            sprintf('Expected status code %d, got %d. Content: %s',
                $expectedStatusCode,
                $this->client->getResponse()->getStatusCode(),
                $this->client->getResponse()->getContent()
            )
        );
    }
}
