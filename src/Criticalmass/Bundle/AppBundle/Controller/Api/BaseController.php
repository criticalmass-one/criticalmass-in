<?php

namespace Criticalmass\Bundle\AppBundle\Controller\Api;

use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseController extends FOSRestController
{
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

        return $this->getJmsSerializer()->deserialize($content, $modelClass, 'json');
    }

    protected function getJmsSerializer(): Serializer
    {
        return $this->get('jms_serializer');
    }
}
