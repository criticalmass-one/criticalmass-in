<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory\QueryFactory;

use App\Criticalmass\DataQuery\Factory\ConflictResolver\ConflictResolver;
use App\Criticalmass\DataQuery\Factory\ValueAssigner\ValueAssignerInterface;
use App\Criticalmass\DataQuery\FieldList\EntityFieldList\EntityField;
use App\Criticalmass\DataQuery\FieldList\EntityFieldList\EntityFieldListFactoryInterface;
use App\Criticalmass\DataQuery\FieldList\QueryFieldList\QueryField;
use App\Criticalmass\DataQuery\FieldList\QueryFieldList\QueryFieldListFactoryInterface;
use App\Criticalmass\DataQuery\Manager\QueryManagerInterface;
use App\Criticalmass\DataQuery\Query\BooleanQuery;
use App\Criticalmass\DataQuery\Query\QueryInterface;
use App\Criticalmass\DataQuery\Query\YearQuery;
use App\Criticalmass\DataQuery\RequestParameterList\RequestParameterList;
use App\Criticalmass\Util\ClassUtil;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QueryFactory implements QueryFactoryInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var string $entityFqcn */
    protected $entityFqcn;

    /** @var QueryManagerInterface $queryManager */
    protected $queryManager;

    /** @var ValueAssignerInterface $valueAssignerInterface */
    protected $valueAssigner;

    /** @var ValidatorInterface $validator */
    protected $validator;

    /** @var EntityFieldListFactoryInterface $entityFieldListFactory */
    protected $entityFieldListFactory;

    /** @var QueryFieldListFactoryInterface $queryFieldListFactory */
    protected $queryFieldListFactory;

    public function __construct(RegistryInterface $registry, QueryManagerInterface $queryManager, ValueAssignerInterface $valueAssigner, ValidatorInterface $validator, EntityFieldListFactoryInterface $entityFieldListFactory, QueryFieldListFactoryInterface $queryFieldListFactory)
    {
        $this->registry = $registry;
        $this->queryManager = $queryManager;
        $this->valueAssigner = $valueAssigner;
        $this->validator = $validator;
        $this->entityFieldListFactory = $entityFieldListFactory;
        $this->queryFieldListFactory = $queryFieldListFactory;
    }

    public function setEntityFqcn(string $entityFqcn): QueryFactoryInterface
    {
        $this->entityFqcn = $entityFqcn;

        return $this;
    }

    public function createFromList(RequestParameterList $requestParameterList): array
    {
        $queryList = $this->findEntityDefaultValuesAsQuery();

        /** @var QueryInterface $queryCandidate */
        foreach ($this->queryManager->getQueryList() as $queryCandidate) {
            $query = $this->checkForQuery(get_class($queryCandidate), $requestParameterList);

            if ($query) {
                $queryList[ClassUtil::getShortname($query)] = $query;
            }
        }

        $queryList = ConflictResolver::resolveConflicts($queryList);

        return $queryList;
    }

    protected function checkForQuery(string $queryFqcn, RequestParameterList $requestParameterList): ?QueryInterface
    {
        $query = new $queryFqcn();

        $queryFieldList = $this->queryFieldListFactory->createForFqcn($queryFqcn);
        $entityFieldList = $this->entityFieldListFactory->createForFqcn($this->entityFqcn);

        /**
         * @var string $fieldName
         * @var array $queryFields
         */
        foreach ($queryFieldList->getList() as $fieldName => $queryFields) {
            /** @var QueryField $queryField */
            foreach ($queryFields as $queryField) {
                $this->valueAssigner->assignQueryPropertyValueFromRequest($requestParameterList, $query, $queryField);
            }
        }

        if ($query instanceof YearQuery) {
            /** @var EntityField $entityField */
            foreach ($entityFieldList->getList() as $entityFields) {
                foreach ($entityFields as $entityField) {
                    if ($entityField->getDateTimePattern() && $entityField->getDateTimeFormat()) {
                        $query
                            ->setDateTimePattern($entityField->getDateTimePattern())
                            ->setDateTimeFormat($entityField->getDateTimeFormat())
                            ->setPropertyName($entityField->getPropertyName());

                        break 2;
                    }
                }
            }
        }

        if (!$this->isQueryValid($query)) {
            return null;
        }

        return $query;
    }

    protected function findEntityDefaultValuesAsQuery(): array
    {
        $defaultValueQueryList = [];
        $entityFieldList = $this->entityFieldListFactory->createForFqcn($this->entityFqcn);

        foreach ($entityFieldList->getList() as $entityFieldName => $entityFields) {
            /** @var EntityField $entityField */
            foreach ($entityFields as $entityField) {
                if ($entityField->hasDefaultQueryBool()) {
                    $booleanQuery = new BooleanQuery();
                    $booleanQuery
                        ->setPropertyName($entityField->getPropertyName())
                        ->setValue($entityField->getDefaultQueryBoolValue());

                    $defaultValueQueryList[] = $booleanQuery;
                }
            }
        }

        return $defaultValueQueryList;
    }

    protected function isQueryValid(QueryInterface $query): bool
    {
        /** @var ConstraintViolationListInterface $constraintViolationList */
        $constraintViolationList = $this->validator->validate($query);

        return $constraintViolationList->count() === 0;
    }
}
