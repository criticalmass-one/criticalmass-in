<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class PhotoApiQueryTest extends AbstractApiControllerTestCase
{
    public function testDefaultListExcludesDeletedPhotos(): void
    {
        $conn = $this->entityManager->getConnection();
        $deletedRows = $conn->fetchAllAssociative('SELECT id FROM photo WHERE deleted = 1');
        $this->assertNotEmpty($deletedRows, 'Fixtures must include at least one deleted photo');
        $deletedIds = array_map(fn(array $row) => (int) $row['id'], $deletedRows);

        $this->client->request('GET', '/api/photo?size=50');

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        $returnedIds = array_column($data, 'id');

        foreach ($deletedIds as $deletedId) {
            $this->assertNotContains($deletedId, $returnedIds, sprintf('Deleted photo %d should not appear in API response', $deletedId));
        }
    }

    public function testDefaultListExcludesDisabledPhotos(): void
    {
        $conn = $this->entityManager->getConnection();
        $disabledRows = $conn->fetchAllAssociative('SELECT id FROM photo WHERE enabled = 0');
        $this->assertNotEmpty($disabledRows, 'Fixtures must include at least one disabled photo');
        $disabledIds = array_map(fn(array $row) => (int) $row['id'], $disabledRows);

        $this->client->request('GET', '/api/photo?size=50');

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        $returnedIds = array_column($data, 'id');

        foreach ($disabledIds as $disabledId) {
            $this->assertNotContains($disabledId, $returnedIds, sprintf('Disabled photo %d should not appear in API response', $disabledId));
        }
    }

    public function testLimitSize(): void
    {
        $this->client->request('GET', '/api/photo?size=2');

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertLessThanOrEqual(2, count($data));
    }

    public function testFilterByCitySlug(): void
    {
        $this->client->request('GET', '/api/photo?citySlug=hamburg&size=50');

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);

        $returnedIds = array_column($data, 'id');

        $conn = $this->entityManager->getConnection();
        $hamburgPhotoIds = $conn->fetchFirstColumn(
            'SELECT p.id FROM photo p JOIN city c ON p.city_id = c.id JOIN cityslug cs ON c.main_slug_id = cs.id WHERE cs.slug = :slug AND p.enabled = 1 AND p.deleted = 0',
            ['slug' => 'hamburg']
        );
        $hamburgPhotoIds = array_map('intval', $hamburgPhotoIds);

        foreach ($returnedIds as $returnedId) {
            $this->assertContains($returnedId, $hamburgPhotoIds, sprintf('Photo %d should belong to Hamburg', $returnedId));
        }
    }

    #[DataProvider('orderParameterProvider')]
    public function testOrderByParameter(string $orderBy, string $direction, ?string $propertyName = null): void
    {
        $apiUri = sprintf('/api/photo?orderBy=%s&orderDirection=%s&size=50', $orderBy, $direction);

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

        $this->assertSame($sorted, $values, sprintf('Photos should be sorted by %s %s', $orderBy, $direction));
    }

    /** @return array<array<string>> */
    public static function orderParameterProvider(): array
    {
        return [
            ['id', 'asc'],
            ['id', 'desc'],
            ['latitude', 'asc'],
            ['latitude', 'desc'],
            ['longitude', 'asc'],
            ['longitude', 'desc'],
            ['views', 'asc'],
            ['views', 'desc'],
            ['creationDateTime', 'asc', 'creation_date_time'],
            ['creationDateTime', 'desc', 'creation_date_time'],
            ['exifCreationDate', 'asc', 'exif_creation_date'],
            ['exifCreationDate', 'desc', 'exif_creation_date'],
        ];
    }
}
