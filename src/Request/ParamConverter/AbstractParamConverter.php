<?php declare(strict_types=1);

namespace App\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractParamConverter implements ParamConverterInterface
{
    public function __construct(protected ManagerRegistry $registry)
    {
    }

    public function supports(ParamConverter $configuration): bool
    {
        $shortname = $this->getEntityShortName();
        $longname = sprintf('App:%s', $shortname);

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
        return sprintf('App\\Entity\\%s', $this->getEntityShortName());
    }

    protected function notFound(ParamConverter $configuration): void
    {
        if (!$configuration->isOptional()) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $configuration->getClass()));
        }
    }
}
