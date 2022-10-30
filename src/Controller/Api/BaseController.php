<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Criticalmass\Api\Error;
use App\Criticalmass\Api\Errors;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    protected ManagerRegistry $managerRegistry;
    protected SerializerInterface $serializer;

    public function __construct(ManagerRegistry $managerRegistry, SerializerInterface $serializer)
    {
        $this->managerRegistry = $managerRegistry;
        $this->serializer = $serializer;
    }

    protected function getDeserializationContext(): DeserializationContext
    {
        $deserializationContext = $this->initSerializerContext(new DeserializationContext());

        return $deserializationContext;
    }

    protected function getSerializationContext(): SerializationContext
    {
        $serializationContext = $this->initSerializerContext(new SerializationContext());

        return $serializationContext;
    }

    protected function initSerializerContext(Context $context): Context
    {
        $context->setSerializeNull(true);

        return $context;
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

    /** @deprecated */
    protected function createError(int $statusCode, string $errorMessage): Response
    {
        $error = new Error($statusCode, $errorMessage);

        $view = View::create();
        $view
            ->setFormat('json')
            ->setData($error)
            ->setStatusCode($statusCode);

        return $this->handleView($view);
    }

    protected function createErrors(int $statusCode, array $errorMessages): Response
    {
        $error = new Errors($statusCode, $errorMessages);

        $view = View::create();
        $view
            ->setFormat('json')
            ->setData($error)
            ->setStatusCode($statusCode);

        return $this->handleView($view);
    }

    protected function createStandardResponse($responseObject, ?SerializationContext $context = null, int $httpStatus = JsonResponse::HTTP_OK, array $headerList = []): JsonResponse
    {
        return new JsonResponse($this->serializer->serialize($responseObject, 'json', $context), $httpStatus, $headerList, true);
    }
}
