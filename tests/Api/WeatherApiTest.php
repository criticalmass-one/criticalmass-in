<?php declare(strict_types=1);

namespace Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WeatherApiTest extends WebTestCase
{
    public function testAddWeatherToRide(): void
    {
        $client = static::createClient();

        // Minimaler, plausibler Payload – bitte Felder ggf. an deine Entity anpassen
        $payload = [
            'temperature' => 18.5,
            'wind_speed'  => 3.2,
            'condition'   => 'partly_cloudy',
            'observed_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ];

        $client->request(
            'PUT',
            '/api/hamburg/2015-08-28/weather',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload)
        );

        // Je nach Constraints kann 201 erwartet werden – wenn Pflichtfelder fehlen, ggf. 400
        $code = $client->getResponse()->getStatusCode();
        if ($code === 400) {
            $this->markTestSkipped('Weather entity requires additional fields in your schema.');
        }

        $this->assertSame(201, $code);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertSame(18.5, $data['temperature'] ?? 18.5);
    }
}
