<?php

namespace Criticalmass\Bundle\AppBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractParamConverter extends DoctrineParamConverter
{
    public function supports(ParamConverter $configuration): bool
    {
        $shortname = $this->getEntityShortName();
        $longname = sprintf('AppBundle:%s', $shortname);

        return $configuration->getClass() === $longname;
    }

    protected function getEntityShortName(): string
    {
        $reflection = new \ReflectionClass($this);

        preg_match('/([A-z]+)ParamConverter/', $reflection->getShortName(), $matches);

        return array_pop($matches);
    }

    protected function getLowercaseEntityShortName(): string
    {
        return strtolower($this->getEntityShortName());
    }

    protected function getEntityFqcn(): string
    {
        return sprintf('Criticalmass\\Bundle\\AppBundle\\Entity\\%s', $this->getEntityShortName());
    }

    protected function notFound(ParamConverter $configuration): void
    {
        if (!$configuration->isOptional()) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $configuration->getClass()));
        }
    }
}
