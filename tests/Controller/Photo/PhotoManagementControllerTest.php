<?php declare(strict_types=1);

namespace Tests\Controller\Photo;

use App\Entity\Photo;
use Tests\Controller\AbstractControllerTestCase;

class PhotoManagementControllerTest extends AbstractControllerTestCase
{
    private function getOwnPhoto(): Photo
    {
        $em = static::getContainer()->get('doctrine')->getManager();

        $photo = $em->getRepository(Photo::class)->findOneBy(['imageName' => 'hamburg_ride_001.jpg']);
        $this->assertNotNull($photo, 'Hamburg photo fixture of testuser should exist');

        return $photo;
    }

    public function testPlaceSinglePhotoPageRendersForOwner(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $photo = $this->getOwnPhoto();

        $client->request('GET', sprintf('/photo/%d/place', $photo->getId()), [], [], [
            'HTTP_REFERER' => 'http://localhost/hamburg',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Foto platzieren', $client->getResponse()->getContent());
    }

    public function testPlaceSinglePhotoPageRendersWithoutReferer(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $photo = $this->getOwnPhoto();

        $client->request('GET', sprintf('/photo/%d/place', $photo->getId()));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testPlaceSinglePhotoSavesCoordinates(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'testuser@criticalmass.in');

        $photo = $this->getOwnPhoto();
        $photoId = $photo->getId();
        $originalLatitude = $photo->getLatitude();
        $originalLongitude = $photo->getLongitude();

        $crawler = $client->request('GET', sprintf('/photo/%d/place', $photoId), [], [], [
            'HTTP_REFERER' => 'http://localhost/hamburg',
        ]);
        $form = $crawler->selectButton('Speichern')->form();
        $form['photo_coord[latitude]'] = '53.5';
        $form['photo_coord[longitude]'] = '10.0';

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/hamburg'));

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->clear();
        $photo = $em->getRepository(Photo::class)->find($photoId);

        $this->assertEqualsWithDelta(53.5, $photo->getLatitude(), 0.0001);
        $this->assertEqualsWithDelta(10.0, $photo->getLongitude(), 0.0001);

        $photo->setLatitude($originalLatitude)->setLongitude($originalLongitude);
        $em->flush();
    }
}
