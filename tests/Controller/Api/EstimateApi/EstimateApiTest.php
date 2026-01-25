<?php declare(strict_types=1);

namespace Tests\Controller\Api\EstimateApi;

use App\Entity\Ride;
use App\Entity\RideEstimate;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class EstimateApiTest extends AbstractApiControllerTestCase
{
    public function testEstimateFixturesExist(): void
    {
        $estimates = $this->entityManager->getRepository(RideEstimate::class)->findAll();

        $this->assertNotEmpty($estimates, 'Estimate fixtures should be loaded');

        /** @var RideEstimate $estimate */
        $estimate = $estimates[0];

        $this->assertNotNull($estimate->getRide());
        $this->assertNotNull($estimate->getEstimatedParticipants());
    }

    public function testEstimateHasCorrectRideAssociation(): void
    {
        $estimates = $this->entityManager->getRepository(RideEstimate::class)->findAll();

        $this->assertNotEmpty($estimates);

        /** @var RideEstimate $estimate */
        foreach ($estimates as $estimate) {
            $ride = $estimate->getRide();
            $this->assertNotNull($ride);
            $this->assertNotNull($ride->getCity());
        }
    }

    public function testCreateEstimateForRide(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findAll();
        $this->assertNotEmpty($rides, 'No rides found in database');

        /** @var Ride $ride */
        $ride = $rides[0];
        $dateString = $ride->getDateTime()->format('Y-m-d');
        $citySlug = $ride->getCity()->getMainSlugString();

        $estimateData = [
            'estimation' => 500,
            'latitude' => $ride->getLatitude(),
            'longitude' => $ride->getLongitude(),
            'source' => 'api-test',
        ];

        $this->client->request(
            'POST',
            '/api/estimate',
            [
                'citySlug' => $citySlug,
                'rideIdentifier' => $dateString,
            ],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($estimateData)
        );

        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        // JMS Serializer uses snake_case naming strategy
        $this->assertArrayHasKey('estimated_participants', $response);
        $this->assertEquals(500, $response['estimated_participants']);
    }

    public function testMultipleEstimatesPerRide(): void
    {
        $estimates = $this->entityManager->getRepository(RideEstimate::class)->findAll();

        $rideEstimateCounts = [];
        foreach ($estimates as $estimate) {
            $rideId = $estimate->getRide()->getId();
            if (!isset($rideEstimateCounts[$rideId])) {
                $rideEstimateCounts[$rideId] = 0;
            }
            $rideEstimateCounts[$rideId]++;
        }

        $maxEstimates = max($rideEstimateCounts);
        $this->assertGreaterThan(1, $maxEstimates, 'At least one ride should have multiple estimates');
    }

    public function testEstimateHasExpectedProperties(): void
    {
        $estimates = $this->entityManager->getRepository(RideEstimate::class)->findAll();

        $this->assertNotEmpty($estimates);

        /** @var RideEstimate $estimate */
        $estimate = $estimates[0];

        $this->assertNotNull($estimate->getId());
        $this->assertNotNull($estimate->getEstimatedParticipants());
        $this->assertNotNull($estimate->getDateTime());
        $this->assertNotNull($estimate->getRide());
    }

    public function testEstimateHasSourceProperty(): void
    {
        $estimates = $this->entityManager->getRepository(RideEstimate::class)->findAll();

        $sources = [];
        foreach ($estimates as $estimate) {
            $source = $estimate->getSource();
            if ($source) {
                $sources[] = $source;
            }
        }

        $this->assertNotEmpty($sources, 'At least one estimate should have a source');
        $this->assertContains('web', $sources);
        $this->assertContains('app', $sources);
    }
}
