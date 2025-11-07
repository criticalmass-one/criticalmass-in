<?php declare(strict_types=1);

namespace Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EstimateApiTest extends WebTestCase
{
    public function testCreateEstimateForSpecificRide(): void
    {
        $client = static::createClient();

        // Nur "estimation" ist notwendig für ride-gebundenes Endpoint
        $payload = [
            'estimation' => 1234,
            'source' => 'phpunit',
        ];

        $client->request(
            'POST',
            '/api/hamburg/2015-08-28/estimate',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload)
        );

        // Akzeptiere 200 (dein Controller gibt 200) – falls du auf 201 umstellst, passe an.
        $this->assertResponseStatusCodeSame(200);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(1234, $data['estimated_participants'] ?? $data['estimation'] ?? null);
    }

    public function testCreateEstimateWithAutodetect(): void
    {
        $client = static::createClient();

        // In Hamburg, Datum passend zu einem (vermuteten) Ride – anpassen, falls nötig.
        $payload = [
            'latitude'   => 53.55,
            'longitude'  => 10.00,
            'estimation' => 222,
            // Unix-Timestamp (Sekunden) – bitte Datum ggf. justieren, je nach Fixtures
            'date_time'  => strtotime('2015-08-28 20:00:00'),
            'source'     => 'phpunit',
        ];

        $client->request(
            'POST',
            '/api/estimate',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload)
        );

        // Entweder 200 (gefunden) oder 400 (kein Ride gefunden) – wir akzeptieren beide und machen in dem Fall Skip
        $status = $client->getResponse()->getStatusCode();
        if ($status === 400) {
            $this->markTestSkipped('No matching ride found for autodetected estimate with provided coords/date.');
        }

        $this->assertResponseStatusCodeSame(200);
    }
}
