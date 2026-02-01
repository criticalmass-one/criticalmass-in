<?php declare(strict_types=1);

namespace Tests\Controller\Api\Query;

use App\Entity\Photo;
use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class DateTimeQueryTest extends AbstractApiControllerTestCase
{
    public function testRideFilterByYear(): void
    {
        $ride = $this->entityManager->getRepository(Ride::class)->findAll()[0];
        $year = (int) $ride->getDateTime()->format('Y');

        $this->client->request('GET', sprintf('/api/ride?year=%d', $year));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $resultList = $this->getJsonResponse();
        $this->assertNotEmpty($resultList);

        foreach ($resultList as $result) {
            $dateTime = (new \DateTime())->setTimestamp($result['date_time']);
            $this->assertEquals($year, (int) $dateTime->format('Y'));
        }
    }

    public function testRideFilterByYearAndMonth(): void
    {
        $ride = $this->entityManager->getRepository(Ride::class)->findAll()[0];
        $year = (int) $ride->getDateTime()->format('Y');
        $month = (int) $ride->getDateTime()->format('n');

        $this->client->request('GET', sprintf('/api/ride?year=%d&month=%d', $year, $month));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $resultList = $this->getJsonResponse();
        $this->assertNotEmpty($resultList);

        foreach ($resultList as $result) {
            $dateTime = (new \DateTime())->setTimestamp($result['date_time']);
            $this->assertEquals(sprintf('%d-%02d', $year, $month), $dateTime->format('Y-m'));
        }
    }

    public function testRideFilterByYearMonthDay(): void
    {
        $ride = $this->entityManager->getRepository(Ride::class)->findAll()[0];
        $year = (int) $ride->getDateTime()->format('Y');
        $month = (int) $ride->getDateTime()->format('n');
        $day = (int) $ride->getDateTime()->format('j');

        $this->client->request('GET', sprintf('/api/ride?year=%d&month=%d&day=%d', $year, $month, $day));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $resultList = $this->getJsonResponse();
        $this->assertNotEmpty($resultList);

        foreach ($resultList as $result) {
            $dateTime = (new \DateTime())->setTimestamp($result['date_time']);
            $this->assertEquals($ride->getDateTime()->format('Y-m-d'), $dateTime->format('Y-m-d'));
        }
    }

    public function testPhotoFilterByYear(): void
    {
        $photo = $this->entityManager->getRepository(Photo::class)->findAll()[0];
        $year = (int) $photo->getExifCreationDate()->format('Y');

        $this->client->request('GET', sprintf('/api/photo?year=%d', $year));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $resultList = $this->getJsonResponse();
        $this->assertNotEmpty($resultList);

        foreach ($resultList as $result) {
            $dateTime = (new \DateTime())->setTimestamp($result['exif_creation_date']);
            $this->assertEquals($year, (int) $dateTime->format('Y'));
        }
    }

    public function testPhotoFilterByYearAndMonth(): void
    {
        $photo = $this->entityManager->getRepository(Photo::class)->findAll()[0];
        $year = (int) $photo->getExifCreationDate()->format('Y');
        $month = (int) $photo->getExifCreationDate()->format('n');

        $this->client->request('GET', sprintf('/api/photo?year=%d&month=%d', $year, $month));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $resultList = $this->getJsonResponse();
        $this->assertNotEmpty($resultList);

        foreach ($resultList as $result) {
            $dateTime = (new \DateTime())->setTimestamp($result['exif_creation_date']);
            $this->assertEquals(sprintf('%d-%02d', $year, $month), $dateTime->format('Y-m'));
        }
    }

    public function testPhotoFilterByYearMonthDay(): void
    {
        $photo = $this->entityManager->getRepository(Photo::class)->findAll()[0];
        $year = (int) $photo->getExifCreationDate()->format('Y');
        $month = (int) $photo->getExifCreationDate()->format('n');
        $day = (int) $photo->getExifCreationDate()->format('j');

        $this->client->request('GET', sprintf('/api/photo?year=%d&month=%d&day=%d', $year, $month, $day));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $resultList = $this->getJsonResponse();
        $this->assertNotEmpty($resultList);

        foreach ($resultList as $result) {
            $dateTime = (new \DateTime())->setTimestamp($result['exif_creation_date']);
            $this->assertEquals($photo->getExifCreationDate()->format('Y-m-d'), $dateTime->format('Y-m-d'));
        }
    }
}
