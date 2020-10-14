<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory\ParameterFactory;

use App\Criticalmass\DataQuery\Factory\ValueAssigner\ValueAssignerInterface;
use App\Criticalmass\DataQuery\FieldList\ParameterFieldList\ParameterField;
use App\Criticalmass\DataQuery\FieldList\ParameterFieldList\ParameterFieldListFactoryInterface;
use App\Criticalmass\DataQuery\Manager\ParameterManagerInterface;
use App\Criticalmass\DataQuery\Parameter\ParameterInterface;
use App\Criticalmass\DataQuery\RequestParameterList\RequestParameterList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ParameterFactory implements ParameterFactoryInterface
{
    /** @var string $entityFqcn */
    protected $entityFqcn;

    /** @var ParameterManagerInterface $parameterManager */
    protected $parameterManager;

    /** @var ValueAssignerInterface $valueAssigner */
    protected $valueAssigner;

    /** @var ValidatorInterface $validator */
    protected $validator;

    /** @var ParameterFieldListFactoryInterface */
    protected $parameterListFactory;

    public function __construct(ParameterManagerInterface $parameterManager, ValueAssignerInterface $valueAssigner, ValidatorInterface $validator, ParameterFieldListFactoryInterface $parameterFieldListFactory)
    {
        $this->parameterManager = $parameterManager;
        $this->valueAssigner = $valueAssigner;
        $this->validator = $validator;
        $this->parameterListFactory = $parameterFieldListFactory;
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
            $parameter = $this->checkForParameter(get_class($parameterCandidate), $requestParameterList);

            if ($parameter) {
                $parameterList[] = $parameter;
            }
        }

        return $parameterList;
    }

    protected function checkForParameter(string $queryFqcn, RequestParameterList $requestParameterList): ?ParameterInterface
    {
        $parameterFieldList = $this->parameterListFactory->createForFqcn($queryFqcn);

        /** @var ParameterInterface $parameter */
        $parameter = new $queryFqcn();
        $parameter->setEntityFqcn($this->entityFqcn);
        
        /** @var ParameterField $parameterField */
        foreach ($parameterFieldList->getList() as $fieldName => $parameterFields) {
            foreach ($parameterFields as $parameterField) {
                $parameter = $this->valueAssigner->assignParameterPropertyValueFromRequest($requestParameterList, $parameter, $parameterField);
            }
        }

        if (!$this->isParameterValid($parameter)) {
            return null;
        }
        
        return $parameter;
    }

    protected function isParameterValid(ParameterInterface $parameter): bool
    {
        $constraintViolationList = $this->validator->validate($parameter);

        return $constraintViolationList->count() === 0;
    }
}
