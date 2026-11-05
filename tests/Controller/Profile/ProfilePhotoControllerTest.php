<?php declare(strict_types=1);

namespace Tests\Controller\Profile;

use Tests\Controller\AbstractControllerTestCase;

class ProfilePhotoControllerTest extends AbstractControllerTestCase
{
    public function testUploadPageRendersForUserWithoutImage(): void
    {
        $client = static::createClient();
        $this->assertNull($this->getUser('admin@criticalmass.in')->getImageName(), 'Fixture user admin has no profile photo');
        $this->loginAs($client, 'admin@criticalmass.in');

        $client->request('GET', '/profile/profilephoto');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Dein Profilfoto', $client->getResponse()->getContent());

        // Ohne Bild entsteht ein Filterpfad ohne Datei; dev wirft dabei (strict_requirements), prod zeigt ein kaputtes Bild.
        $this->assertStringNotContainsString('user_profile_photo_medium/"', $client->getResponse()->getContent());
    }
}
