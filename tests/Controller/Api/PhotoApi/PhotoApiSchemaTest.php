<?php declare(strict_types=1);

namespace Tests\Controller\Api\PhotoApi;

use App\Entity\Photo;
use Tests\Controller\Api\Schema\ApiSchemaDefinitions;
use Tests\Controller\Api\Schema\JsonStructureValidator;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

#[TestDox('Photo API Schema Validation')]
class PhotoApiSchemaTest extends AbstractApiControllerTestCase
{
    #[TestDox('GET /api/photo returns array of photos matching PHOTO_SCHEMA')]
    public function testPhotoListResponseSchema(): void
    {
        $this->client->request('GET', '/api/photo');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertIsArray($response);
        $this->assertNotEmpty($response, 'Photo list should not be empty');

        foreach ($response as $index => $photo) {
            $this->assertIsArray($photo, "Photo at index {$index} should be an array");
            JsonStructureValidator::assertMatchesSchema(
                ApiSchemaDefinitions::PHOTO_SCHEMA,
                $photo,
                "photos[{$index}]"
            );
        }
    }

    #[TestDox('Photo creationDateTime is a Unix timestamp')]
    public function testPhotoCreationDateTimeIsUnixTimestamp(): void
    {
        $this->client->request('GET', '/api/photo?size=1');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertNotEmpty($response);

        $photo = $response[0];
        $this->assertArrayHasKey('creation_date_time', $photo);
        $this->assertIsInt($photo['creation_date_time'], 'creationDateTime should be a Unix timestamp');
        $this->assertGreaterThan(0, $photo['creation_date_time'], 'creationDateTime should be a positive timestamp');
    }

    #[TestDox('Photo coordinates are valid when present')]
    public function testPhotoCoordinatesAreValidWhenPresent(): void
    {
        $this->client->request('GET', '/api/photo?size=10');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $photo) {
            if ($photo['latitude'] !== null) {
                $this->assertGreaterThanOrEqual(-90, $photo['latitude']);
                $this->assertLessThanOrEqual(90, $photo['latitude']);
            }
            if ($photo['longitude'] !== null) {
                $this->assertGreaterThanOrEqual(-180, $photo['longitude']);
                $this->assertLessThanOrEqual(180, $photo['longitude']);
            }
        }
    }

    #[TestDox('Photo imageName is a non-empty string')]
    public function testPhotoImageNameIsNonEmptyString(): void
    {
        $this->client->request('GET', '/api/photo?size=10');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $photo) {
            $this->assertIsString($photo['image_name']);
            $this->assertNotEmpty($photo['image_name']);
        }
    }

    #[TestDox('Photo EXIF ISO is a valid integer when present')]
    public function testPhotoExifIsoIsValidWhenPresent(): void
    {
        $this->client->request('GET', '/api/photo?size=20');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $photo) {
            if (isset($photo['exif_iso']) && $photo['exif_iso'] !== null) {
                $this->assertIsInt($photo['exif_iso']);
                $this->assertGreaterThan(0, $photo['exif_iso'], 'ISO should be a positive integer');
            }
        }
    }

    #[TestDox('GET /api/photo supports size parameter')]
    public function testPhotoListSizeParameter(): void
    {
        $this->client->request('GET', '/api/photo?size=5');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();
        $this->assertLessThanOrEqual(5, count($response));
    }

    #[TestDox('Photo imageSize is a positive integer when present')]
    public function testPhotoImageSizeIsPositiveWhenPresent(): void
    {
        $this->client->request('GET', '/api/photo?size=10');
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        foreach ($response as $photo) {
            if (isset($photo['image_size']) && $photo['image_size'] !== null) {
                $this->assertIsInt($photo['image_size']);
                $this->assertGreaterThan(0, $photo['image_size']);
            }
        }
    }
}
