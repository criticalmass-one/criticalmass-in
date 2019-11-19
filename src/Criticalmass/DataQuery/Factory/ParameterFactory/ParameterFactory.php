<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory\ParameterFactory;

use App\Criticalmass\DataQuery\Annotation\Sortable;
use App\Criticalmass\DataQuery\AnnotationHandler\AnnotationHandlerInterface;
use App\Criticalmass\DataQuery\Exception\TargetPropertyNotSortableException;
use App\Criticalmass\DataQuery\Exception\ValidationException;
use App\Criticalmass\DataQuery\Factory\ValueAssigner\ValueAssignerInterface;
use App\Criticalmass\DataQuery\Manager\ParameterManagerInterface;
use App\Criticalmass\DataQuery\Parameter\ParameterInterface;
use App\Criticalmass\DataQuery\Parameter\PropertyTargetingParameterInterface;
use App\Criticalmass\DataQuery\Property\ParameterProperty;
use App\Criticalmass\DataQuery\RequestParameterList\RequestParameterList;
use App\Criticalmass\Util\ClassUtil;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    /** @var ValidatorInterface $validator */
    protected $validator;

    public function __construct(AnnotationHandlerInterface $annotationHandler, ParameterManagerInterface $parameterManager, ValueAssignerInterface $valueAssigner, ValidatorInterface $validator)
    {
        $this->annotationHandler = $annotationHandler;
        $this->parameterManager = $parameterManager;
        $this->valueAssigner = $valueAssigner;
        $this->validator = $validator;
    }

    public function setEntityFqcn(string $entityFqcn): ParameterFactoryInterface
    {
        $this->entityFqcn = $entityFqcn;

        return $this;
    }

    public function createFromList(RequestParameterList $requestParameterList): array
    {
        $parameterList = [];

        /** @var ParameterInterface $parameter */
        foreach ($this->parameterManager->getParameterList() as $parameterCandidate) {
            $parameterUnderTest = $this->checkForParameter(get_class($parameterCandidate), $requestParameterList);

            if ($parameterUnderTest) {
                /** @var ConstraintViolationListInterface $constraintViolationList */
                $constraintViolationList = $this->validator->validate($parameterUnderTest);

                if (0 === $constraintViolationList->count()) {
                    $key = ClassUtil::getShortname($parameterUnderTest);
                    $parameterList[$key] = $parameterUnderTest;
                } else {
                    $firstMessage = $constraintViolationList->get(0);
                    throw new ValidationException($firstMessage->getMessage());
                }
            }
        }

        return $parameterList;
    }

    protected function checkForParameter(string $queryFqcn, RequestParameterList $requestParameterList): ?ParameterInterface
    {
        $requiredParameterableList = $this->annotationHandler->listParameterRequiredMethods($queryFqcn);

        /** @var ParameterInterface $parameter */
        $parameter = new $queryFqcn();

        /** @var ParameterProperty $requiredParameterProperty */
        foreach ($requiredParameterableList as $requiredParameterProperty) {
            if (!$requestParameterList->has($requiredParameterProperty->getParameterName())) {
                return null;
            }

            $parameter = $this->valueAssigner->assignParameterPropertyValue($requestParameterList, $parameter, $requiredParameterProperty);

            if ($parameter instanceof PropertyTargetingParameterInterface) {
                /** @var PropertyTargetingParameterInterface $parameter */
                $methodName = sprintf('get%s', ucfirst($parameter->getPropertyName()));

                if ($requiredParameterProperty->hasRequiredSortableTargetEntity() && !$this->annotationHandler->hasEntityAnnotatedMethod($this->entityFqcn, $methodName, Sortable::class)) {
                    throw new TargetPropertyNotSortableException($parameter->getPropertyName(), $this->entityFqcn);
                }
            }
        }

        return $parameter;
    }
}
