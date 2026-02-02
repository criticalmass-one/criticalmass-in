<?php declare(strict_types=1);

namespace Tests\Controller\Api\TrackApi;

use App\Entity\Track;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class TrackApiQueryTest extends AbstractApiControllerTestCase
{
    public function testDefaultListExcludesDeletedTracks(): void
    {
        // Query directly via DBAL to bypass any Doctrine filters
        $conn = $this->entityManager->getConnection();
        $deletedRows = $conn->fetchAllAssociative('SELECT id FROM track WHERE deleted = 1');
        $this->assertNotEmpty($deletedRows, 'Fixtures must include at least one deleted track');
        $deletedIds = array_map(fn(array $row) => (int) $row['id'], $deletedRows);

        $this->client->request('GET', '/api/track?size=50');

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        $returnedIds = array_column($data, 'id');

        foreach ($deletedIds as $deletedId) {
            $this->assertNotContains($deletedId, $returnedIds, sprintf('Deleted track %d should not appear in API response', $deletedId));
        }
    }

    public function testDefaultListExcludesDisabledTracks(): void
    {
        // Query directly via DBAL to bypass any Doctrine filters
        $conn = $this->entityManager->getConnection();
        $disabledRows = $conn->fetchAllAssociative('SELECT id FROM track WHERE enabled = 0');
        $this->assertNotEmpty($disabledRows, 'Fixtures must include at least one disabled track');
        $disabledIds = array_map(fn(array $row) => (int) $row['id'], $disabledRows);

        $this->client->request('GET', '/api/track?size=50');

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        $returnedIds = array_column($data, 'id');

        foreach ($disabledIds as $disabledId) {
            $this->assertNotContains($disabledId, $returnedIds, sprintf('Disabled track %d should not appear in API response', $disabledId));
        }
    }

    public function testFilterByCitySlug(): void
    {
        $this->client->request('GET', '/api/track?citySlug=hamburg&size=50');

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        $returnedIds = array_column($data, 'id');

        $allTracks = $this->entityManager->getRepository(Track::class)->findBy(['enabled' => true, 'deleted' => false]);

        foreach ($allTracks as $track) {
            if ($track->getRide() && $track->getRide()->getCity()) {
                $citySlug = $track->getRide()->getCity()->getMainSlugString();

                if (in_array($track->getId(), $returnedIds)) {
                    $this->assertSame('hamburg', $citySlug, sprintf('Track %d should belong to Hamburg', $track->getId()));
                }
            }
        }
    }

    public function testLimitSize(): void
    {
        $this->client->request('GET', '/api/track?size=2');

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertLessThanOrEqual(2, count($data));
    }

    public function testOrderByStartDateTimeAsc(): void
    {
        $this->client->request('GET', '/api/track?orderBy=startDateTime&orderDirection=asc&size=50');

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        $values = array_column($data, 'start_date_time');
        $sorted = $values;
        sort($sorted);

        $this->assertSame($sorted, $values, 'Tracks should be sorted by startDateTime ascending');
    }

    public function testOrderByStartDateTimeDesc(): void
    {
        $this->client->request('GET', '/api/track?orderBy=startDateTime&orderDirection=desc&size=50');

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        $values = array_column($data, 'start_date_time');
        $sorted = $values;
        rsort($sorted);

        $this->assertSame($sorted, $values, 'Tracks should be sorted by startDateTime descending');
    }

    public function testOrderByDistance(): void
    {
        $this->client->request('GET', '/api/track?orderBy=distance&orderDirection=asc&size=50');

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        $values = array_column($data, 'distance');
        $sorted = $values;
        sort($sorted);

        $this->assertSame($sorted, $values, 'Tracks should be sorted by distance ascending');
    }

    #[DataProvider('orderParameterProvider')]
    public function testOrderByParameter(string $orderBy, string $direction, ?string $propertyName = null): void
    {
        $apiUri = sprintf('/api/track?orderBy=%s&orderDirection=%s&size=50', $orderBy, $direction);

        $this->client->request('GET', $apiUri);

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        if (!$propertyName) {
            $propertyName = $orderBy;
        }

        $values = array_column($data, $propertyName);

        if (empty($values)) {
            $this->markTestSkipped("No values found for property: $propertyName");
        }

        $sorted = $values;
        if ($direction === 'asc') {
            sort($sorted);
        } else {
            rsort($sorted);
        }

        $this->assertSame($sorted, $values, sprintf('Tracks should be sorted by %s %s', $orderBy, $direction));
    }

    public static function orderParameterProvider(): array
    {
        return [
            ['id', 'asc'],
            ['id', 'desc'],
            ['creationDateTime', 'asc', 'creation_date_time'],
            ['creationDateTime', 'desc', 'creation_date_time'],
            ['startDateTime', 'asc', 'start_date_time'],
            ['startDateTime', 'desc', 'start_date_time'],
            ['endDateTime', 'asc', 'end_date_time'],
            ['endDateTime', 'desc', 'end_date_time'],
            ['distance', 'asc'],
            ['distance', 'desc'],
            ['points', 'asc'],
            ['points', 'desc'],
        ];
    }
}
