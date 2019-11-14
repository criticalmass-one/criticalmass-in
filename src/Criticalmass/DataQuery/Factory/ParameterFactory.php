<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory;

use App\Criticalmass\DataQuery\AnnotationHandler\AnnotationHandlerInterface;
use App\Criticalmass\DataQuery\Manager\ParameterManagerInterface;
use App\Criticalmass\DataQuery\Parameter\ParameterInterface;
use App\Criticalmass\DataQuery\Property\ParameterProperty;
use App\Criticalmass\DataQuery\Property\QueryProperty;
use App\Criticalmass\Util\ClassUtil;
use Symfony\Component\HttpFoundation\Request;

class ParameterFactory implements ParameterFactoryInterface
{
    /** @var string $entityFqcn */
    protected $entityFqcn;

    /** @var AnnotationHandlerInterface */
    protected $annotationHandler;

    /** @var ParameterManagerInterface $parameterManager */
    protected $parameterManager;

    /** @var ValueAssignerInterface $valueAssigner */
    protected $valueAssigner;

    public function __construct(AnnotationHandlerInterface $annotationHandler, ParameterManagerInterface $parameterManager, ValueAssignerInterface $valueAssigner)
    {
        $this->annotationHandler = $annotationHandler;
        $this->parameterManager = $parameterManager;
        $this->valueAssigner = $valueAssigner;
    }

    public function setEntityFqcn(string $entityFqcn)
    {
        $this->entityFqcn = $entityFqcn;

        return $this;
    }

    public function createFromRequest(Request $request): array
    {
        $parameterList = [];

        /** @var ParameterInterface $parameter */
        foreach ($this->parameterManager->getParameterList() as $parameterCandidate) {
            $parameterUnderTest = $this->checkForParameter(get_class($parameterCandidate), $request);

            if ($parameterUnderTest) {
                $parameterList[ClassUtil::getShortname($parameterUnderTest)] = $parameterUnderTest;
            }
        }

        dump($parameterList);
        return $parameterList;
    }

    protected function checkForParameter(string $queryFqcn, Request $request): ?ParameterInterface
    {
        $requiredParameterableList = $this->annotationHandler->listParameterRequiredMethods($queryFqcn);

        $requiredPropertiesFound = true;

        /** @var QueryProperty $requiredQuerieableMethod */
        foreach ($requiredParameterableList as $requiredQuerieableMethod) {
            if (!$request->query->has($requiredQuerieableMethod->getParameterName())) {
                $requiredPropertiesFound = false;

                break;
            }
        }

        if ($requiredPropertiesFound) {
            /** @var ParameterProperty $requiredParameterProperty */
            foreach ($requiredParameterableList as $requiredParameterProperty) {
                $parameter = new $queryFqcn();

                $this->valueAssigner->assignParameterPropertyValue($request, $parameter, $requiredParameterProperty);

                return $parameter;
            }
        }

        return null;
    }
}
