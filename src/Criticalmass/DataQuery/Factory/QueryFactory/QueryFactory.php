<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory\QueryFactory;

use App\Criticalmass\DataQuery\AnnotationHandler\AnnotationHandlerInterface;
use App\Criticalmass\DataQuery\EntityFieldList\EntityFieldListFactoryInterface;
use App\Criticalmass\DataQuery\Factory\ConflictResolver\ConflictResolver;
use App\Criticalmass\DataQuery\Factory\ValueAssigner\ValueAssignerInterface;
use App\Criticalmass\DataQuery\Manager\QueryManagerInterface;
use App\Criticalmass\DataQuery\Property\EntityBooleanValueProperty;
use App\Criticalmass\DataQuery\Query\BooleanQuery;
use App\Criticalmass\DataQuery\Query\QueryInterface;
use App\Criticalmass\DataQuery\QueryFieldList\QueryField;
use App\Criticalmass\DataQuery\QueryFieldList\QueryFieldListFactoryInterface;
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

    /** @var AnnotationHandlerInterface $annotationHandler */
    protected $annotationHandler;

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

    public function __construct(RegistryInterface $registry, AnnotationHandlerInterface $annotationHandler, QueryManagerInterface $queryManager, ValueAssignerInterface $valueAssigner, ValidatorInterface $validator, EntityFieldListFactoryInterface $entityFieldListFactory, QueryFieldListFactoryInterface $queryFieldListFactory)
    {
        $this->registry = $registry;
        $this->annotationHandler = $annotationHandler;
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
        $entityFieldList = $this->entityFieldListFactory->createForFqcn($this->entityFqcn);

        /** @var QueryInterface $queryCandidate */
        foreach ($this->queryManager->getQueryList() as $queryCandidate) {
            $query = $this->checkForQuery(get_class($queryCandidate), $requestParameterList);

            if ($query) {
                $queryList[ClassUtil::getShortname($query)] = $query;
            }
        }

        $queryList = ConflictResolver::resolveConflicts($queryList);

        dump($queryList);
        die;
        return $queryList;
    }

    protected function checkForQuery(string $queryFqcn, RequestParameterList $requestParameterList): ?QueryInterface
    {
        $query = new $queryFqcn();

        $queryFieldList = $this->queryFieldListFactory->createForFqcn($queryFqcn);

        /**
         * @var string $fieldName
         * @var array $queryFields
         */
        foreach ($queryFieldList->getList() as $fieldName => $queryFields) {
            /** @var QueryField $queryField */
            foreach ($queryFields as $queryField) {
                $this->valueAssigner->assignQueryPropertyValue($requestParameterList, $query, $queryField);
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
        $entityDefaultValueList = $this->annotationHandler->listEntityDefaultValues($this->entityFqcn);

        /** @var EntityBooleanValueProperty $entityDefaultValue */
        foreach ($entityDefaultValueList as $entityDefaultValue) {
            $booleanQuery = new BooleanQuery();
            $booleanQuery
                ->setPropertyName($entityDefaultValue->getPropertyName())
                ->setValue($entityDefaultValue->getValue());

            $defaultValueQueryList[] = $booleanQuery;
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
