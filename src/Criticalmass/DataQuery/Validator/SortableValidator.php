<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Validator;

use App\Criticalmass\DataQuery\FieldList\EntityFieldList\EntityField;
use App\Criticalmass\DataQuery\FieldList\EntityFieldList\EntityFieldList;
use App\Criticalmass\DataQuery\FieldList\EntityFieldList\EntityFieldListFactoryInterface;
use App\Criticalmass\DataQuery\Parameter\ParameterInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SortableValidator extends ConstraintValidator
{
    /** @var EntityFieldListFactoryInterface $entityFieldListFactory */
    protected $entityFieldListFactory;

    public function __construct(EntityFieldListFactoryInterface $entityFieldListFactory)
    {
        $this->entityFieldListFactory = $entityFieldListFactory;
    }

    public function validate($entityTargetPropertyName, Constraint $constraint): void
    {
        if (!$entityTargetPropertyName) {
            return;
        }
        
        /** @var ParameterInterface $parameter */
        $parameter = $this->context->getRoot();

        /** @var EntityFieldList $fieldList */
        $entityFieldList = $this->entityFieldListFactory->createForFqcn($parameter->getEntityFqcn());

        if (!$entityFieldList->hasField($entityTargetPropertyName)) {
            $this->buildViolation($entityTargetPropertyName, $constraint, $parameter);

            return;
        }

        $entityTargetPropertySortable = false;

        /** @var EntityField $entityField */
        foreach ($entityFieldList->getList()[$entityTargetPropertyName] as $entityField) {
            if ($entityField->isSortable()) {
                $entityTargetPropertySortable = true;

                break;
            }
        }

        if (!$entityTargetPropertySortable) {
            $this->buildViolation($entityTargetPropertyName, $constraint, $parameter);
        }
    }

    protected function buildViolation($entityTargetPropertyName, Constraint $constraint, ParameterInterface $parameter): void
    {
        $this
            ->context->buildViolation($constraint->message)
            ->setParameter('{{ entityTargetPropertyName }}', $entityTargetPropertyName)
            ->setParameter('{{ entityFqcn }}', $parameter->getEntityFqcn())
            ->addViolation();
    }
}
