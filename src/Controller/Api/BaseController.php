<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use MalteHuebner\DataQueryBundle\PaginatedResult\PaginatedResult;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseController extends AbstractController
{
    public function __construct(
        protected readonly ManagerRegistry $managerRegistry,
        protected readonly CriticalSerializerInterface $serializer
    )
    {

    }

    protected function deserializeRequest(Request $request, string $modelClass, array $context = [])
    {
        $content = null;

        if ($request->isMethod(Request::METHOD_GET)) {
            $content = $request->getQueryString();
        } else {
            $content = $request->getContent();
        }

        if (!isset($context['groups'])) {
            $context['groups'] = ['api-write'];
        }

        return $this->serializer->deserialize($content, $modelClass, 'json', $context);
    }

    protected function deserializeRequestInto(Request $request, object $target, array $context = []): object
    {
        if (!isset($context['groups'])) {
            $context['groups'] = ['api-write'];
        }

        return $this->serializer->deserializeInto($request->getContent(), $target, 'json', $context);
    }

    protected function createErrors(int $statusCode, array $errorMessages): JsonResponse
    {
        // Build the payload directly instead of running the Errors DTO through
        // CriticalSerializer: that DTO exposes no getters/groups, so it
        // serialized to an empty "[]", and the string was then double-encoded
        // because the JSON flag was missing — every API error came back as
        // "[]" with no message.
        return new JsonResponse(['errors' => $errorMessages], $statusCode);
    }

    protected function createStandardResponse($responseObject, array $context = [], int $httpStatus = JsonResponse::HTTP_OK, array $headerList = []): JsonResponse
    {
        return new JsonResponse($this->serializer->serialize($responseObject, 'json', $context), $httpStatus, $headerList, true);
    }

    /**
     * @param array<string, mixed> $context
     */
    protected function createPaginatedResponse(PaginatedResult $paginatedResult, array $context = []): JsonResponse
    {
        $data = [];

        foreach ($paginatedResult->getData() as $item) {
            $data[] = json_decode($this->serializer->serialize($item, 'json', $context), true);
        }

        $response = [
            'data' => $data,
            'meta' => [
                'page' => $paginatedResult->getPage(),
                'size' => $paginatedResult->getSize(),
                'totalItems' => $paginatedResult->getTotalItems(),
                'totalPages' => $paginatedResult->getTotalPages(),
            ],
        ];

        return new JsonResponse($response);
    }
}
