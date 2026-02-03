<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\CityActivity;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

class CityActivityController extends BaseController
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        CriticalSerializerInterface $serializer,
    ) {
        parent::__construct($managerRegistry, $serializer);
    }

    #[Route(path: '/api/city/{citySlug}/activity', name: 'caldera_criticalmass_rest_city_activity_create', methods: ['POST'])]
    #[OA\Tag(name: 'CityActivity')]
    #[OA\RequestBody(description: 'JSON representation of the city activity data', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 201, description: 'Activity score created successfully')]
    public function createAction(Request $request, City $city): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }

        $this->validateActivityData($data);

        $cityActivity = $this->createCityActivity($city, $data);

        $this->managerRegistry->getManager()->persist($cityActivity);

        $city->setActivityScore($data['score']);

        $this->managerRegistry->getManager()->flush();

        return $this->createStandardResponse($cityActivity, [], JsonResponse::HTTP_CREATED);
    }

    /** @param array<string, mixed> $data */
    private function validateActivityData(array $data): void
    {
        if (!isset($data['score'])) {
            throw new BadRequestHttpException('Missing required field: score');
        }

        if ($data['score'] < 0.0 || $data['score'] > 1.0) {
            throw new BadRequestHttpException('Score must be between 0.0 and 1.0');
        }

        if (!isset($data['details']) || !is_array($data['details'])) {
            throw new BadRequestHttpException('Missing required field: details');
        }

        $requiredSignalTypes = ['participation', 'photo', 'track', 'social_feed'];
        $foundSignalTypes = [];

        foreach ($data['details'] as $detail) {
            if (isset($detail['signalType'])) {
                $foundSignalTypes[] = $detail['signalType'];
            }
        }

        foreach ($requiredSignalTypes as $requiredType) {
            if (!in_array($requiredType, $foundSignalTypes, true)) {
                throw new BadRequestHttpException(sprintf('Missing required signal type: %s', $requiredType));
            }
        }
    }

    /** @param array<string, mixed> $data */
    private function createCityActivity(City $city, array $data): CityActivity
    {
        $cityActivity = new CityActivity();
        $cityActivity->setCity($city);
        $cityActivity->setScore($data['score']);

        $detailsByType = [];
        foreach ($data['details'] as $detail) {
            $detailsByType[$detail['signalType']] = $detail;
        }

        $participation = $detailsByType['participation'];
        $cityActivity->setParticipationScore($participation['normalizedScore']);
        $cityActivity->setParticipationRawCount($participation['rawCount']);

        $photo = $detailsByType['photo'];
        $cityActivity->setPhotoScore($photo['normalizedScore']);
        $cityActivity->setPhotoRawCount($photo['rawCount']);

        $track = $detailsByType['track'];
        $cityActivity->setTrackScore($track['normalizedScore']);
        $cityActivity->setTrackRawCount($track['rawCount']);

        $socialFeed = $detailsByType['social_feed'];
        $cityActivity->setSocialFeedScore($socialFeed['normalizedScore']);
        $cityActivity->setSocialFeedRawCount($socialFeed['rawCount']);

        if (isset($data['calculatedAt'])) {
            $cityActivity->setCreatedAt(new \DateTimeImmutable($data['calculatedAt']));
        }

        return $cityActivity;
    }
}
