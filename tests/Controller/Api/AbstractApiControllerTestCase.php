<?php declare(strict_types=1);

namespace Tests\Controller\Api;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractApiControllerTestCase extends WebTestCase
{
    protected ?KernelBrowser $client = null;
    protected ?EntityManagerInterface $entityManager = null;
    protected static bool $fixturesLoaded = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        if (!static::$fixturesLoaded) {
            $this->loadFixtures();
            static::$fixturesLoaded = true;
        }
    }

    protected function loadFixtures(): void
    {
        $loader = new Loader();

        $fixtureClasses = $this->getFixtureClasses();

        foreach ($fixtureClasses as $fixtureClass) {
            $loader->addFixture(new $fixtureClass());
        }

        $connection = $this->entityManager->getConnection();

        // Disable foreign key checks for MySQL
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0');

        $purger = new ORMPurger($this->entityManager);
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());

        // Re-enable foreign key checks
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function getFixtureClasses(): array
    {
        return [
            \App\DataFixtures\RegionFixtures::class,
            \App\DataFixtures\UserFixtures::class,
            \App\DataFixtures\CityFixtures::class,
            \App\DataFixtures\LocationFixtures::class,
            \App\DataFixtures\SocialNetworkProfileFixtures::class,
            \App\DataFixtures\CityCycleFixtures::class,
            \App\DataFixtures\RideFixtures::class,
            \App\DataFixtures\PhotoFixtures::class,
            \App\DataFixtures\WeatherFixtures::class,
            \App\DataFixtures\RideEstimateFixtures::class,
            \App\DataFixtures\TrackFixtures::class,
        ];
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
