<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\Rating\Calculator\RatingCalculatorInterface;
use App\Entity\Rating;
use App\Entity\Ride;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class RatingController extends BaseController
{
    /**
     * Get all ratings for a specific ride.
     *
     * Returns a list of all ratings submitted for the specified ride.
     * Path parameters: citySlug (string), rideIdentifier (date or slug string).
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}/rating', name: 'caldera_criticalmass_api_rating_list', methods: ['GET'])]
    #[OA\Tag(name: 'Rating')]
    #[OA\Response(response: 200, description: 'Returns list of ratings for the ride')]
    public function listAction(Ride $ride): JsonResponse
    {
        $ratingList = $this->managerRegistry->getRepository(Rating::class)->findBy(['ride' => $ride]);

        return $this->createStandardResponse($ratingList, ['groups' => 'rating-list']);
    }

    /**
     * Get average rating for a specific ride.
     *
     * Returns the calculated average rating (1-5 stars) for the specified ride.
     * Returns null if no ratings exist.
     * Path parameters: citySlug (string), rideIdentifier (date or slug string).
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}/rating/average', name: 'caldera_criticalmass_api_rating_average', methods: ['GET'])]
    #[OA\Tag(name: 'Rating')]
    #[OA\Response(response: 200, description: 'Returns average rating for the ride')]
    public function averageAction(Ride $ride, RatingCalculatorInterface $ratingCalculator): JsonResponse
    {
        $averageRating = $ratingCalculator->calculateRide($ride);
        $ratingCount = count($this->managerRegistry->getRepository(Rating::class)->findBy(['ride' => $ride]));

        return $this->createStandardResponse([
            'average' => $averageRating,
            'count' => $ratingCount,
        ]);
    }
}
