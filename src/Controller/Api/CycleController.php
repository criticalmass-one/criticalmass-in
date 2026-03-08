<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Region;
use App\Repository\CityCycleRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CycleController extends BaseController
{
    /**
     * Returns a list of city cycles.
     */
    #[Route(path: '/api/cycles', name: 'caldera_criticalmass_rest_cycles_list', methods: ['GET'], priority: 200)]
    #[OA\Tag(name: 'Cycles')]
    #[OA\Parameter(name: 'citySlug', in: 'query', description: 'Provide a city slug', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'regionSlug', in: 'query', description: 'Provide a region slug', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'validFrom', in: 'query', description: 'Only retrieve cycles valid after the provided date', schema: new OA\Schema(type: 'string', format: 'date'))]
    #[OA\Parameter(name: 'validUntil', in: 'query', description: 'Only retrieve cycles valid before the provided date', schema: new OA\Schema(type: 'string', format: 'date'))]
    #[OA\Parameter(name: 'validNow', in: 'query', description: 'Only retrieve cycles valid for the current month', schema: new OA\Schema(type: 'boolean'))]
    #[OA\Parameter(name: 'dayOfWeek', in: 'query', description: 'Limit the results to this day of week', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'weekOfMonth', in: 'query', description: 'Limit the results to this week of month', schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listAction(
        Request $request,
        ?City $city = null,
        ?Region $region = null
    ): JsonResponse {
        $validNow = $request->query->getBoolean('validNow');
        $dayOfWeek = $request->query->getInt('dayOfWeek');
        $weekOfMonth = $request->query->getInt('weekOfMonth');
        $validFromString = $request->query->getAlnum('validFrom');
        $validUntilString = $request->query->getAlnum('validUntil');

        if ($validFromString) {
            $validFrom = new \DateTime($validFromString);
        } else {
            $validFrom = null;
        }

        if ($validUntilString) {
            $validUntil = new \DateTime($validUntilString);
        } else {
            $validUntil = null;
        }

        $cycleList = $this->managerRegistry->getRepository(CityCycle::class)->findForApi($city, $region, $validFrom, $validUntil, $validNow, $dayOfWeek, $weekOfMonth);

        return $this->createStandardResponse($cycleList);
    }

    /**
     * Retrieve a list of cycles assigned to a city.
     */
    #[Route(path: '/api/{citySlug}/cycles', name: 'caldera_criticalmass_rest_cycles_citylist', methods: ['GET'], priority: 190)]
    #[OA\Tag(name: 'Cycles')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listCyclesCityAction(City $city, CityCycleRepository $cityCycleRepository): JsonResponse
    {
        $cycleList = $cityCycleRepository->findByCity($city);

        return $this->createStandardResponse($cycleList);
    }

    /**
     * Create a new cycle and assign it to the provided city.
     */
    #[Route(path: '/api/{citySlug}/cycles', name: 'caldera_criticalmass_rest_cycles_create', methods: ['PUT'], priority: 190)]
    #[OA\Tag(name: 'Cycles')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(description: 'JSON representation of the cycle to create', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Returned when successfully created')]
    public function createCycleAction(Request $request, City $city, ValidatorInterface $validator): JsonResponse
    {
        $newCycle = $this->deserializeRequest($request, CityCycle::class);

        $newCycle
            ->setCity($city)
            ->setCreatedAt(new \DateTime());

        $errors = $validator->validate($newCycle);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $manager = $this->managerRegistry->getManager();
        $manager->persist($newCycle);
        $manager->flush();

        return $this->createStandardResponse($newCycle);
    }
}
