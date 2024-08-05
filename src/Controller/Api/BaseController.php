<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Criticalmass\Api\Errors;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class BaseController extends AbstractController
{
    public function __construct(
        protected readonly ManagerRegistry $managerRegistry,
        protected readonly SerializerInterface $serializer,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        parent::__construct($authorizationChecker);
    }

    protected function deserializeRequest(Request $request, string $modelClass)
    {
        $content = null;

        if ($request->isMethod(Request::METHOD_GET)) {
            $content = $request->getQueryString();
        } else {
            $content = $request->getContent();
        }

        return $this->serializer->deserialize($content, $modelClass, 'json');
    }

    protected function createErrors(int $statusCode, array $errorMessages): JsonResponse
    {
        $error = new Errors($statusCode, $errorMessages);

        return new JsonResponse($this->serializer->serialize($error, 'json'), $statusCode);
    }

    protected function createStandardResponse($responseObject, array $context = [], int $httpStatus = JsonResponse::HTTP_OK, array $headerList = []): JsonResponse
    {
        return new JsonResponse($this->serializer->serialize($responseObject, 'json', $context), $httpStatus, $headerList, true);
    }
}
