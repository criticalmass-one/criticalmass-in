<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory\QueryFactory;

use App\Criticalmass\DataQuery\AnnotationHandler\AnnotationHandlerInterface;
use App\Criticalmass\DataQuery\EntityFieldList\EntityFieldListFactoryInterface;
use App\Criticalmass\DataQuery\Factory\ConflictResolver\ConflictResolver;
use App\Criticalmass\DataQuery\Factory\ValueAssigner\ValueAssignerInterface;
use App\Criticalmass\DataQuery\Manager\QueryManagerInterface;
use App\Criticalmass\DataQuery\Property\EntityBooleanValueProperty;
use App\Criticalmass\DataQuery\Property\QueryProperty;
use App\Criticalmass\DataQuery\Query\BooleanQuery;
use App\Criticalmass\DataQuery\Query\QueryInterface;
use App\Criticalmass\DataQuery\RequestParameterList\RequestParameterList;
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

    public function __construct(RegistryInterface $registry, AnnotationHandlerInterface $annotationHandler, QueryManagerInterface $queryManager, ValueAssignerInterface $valueAssigner, ValidatorInterface $validator, EntityFieldListFactoryInterface $entityFieldListFactory)
    {
        $this->registry = $registry;
        $this->annotationHandler = $annotationHandler;
        $this->queryManager = $queryManager;
        $this->valueAssigner = $valueAssigner;
        $this->validator = $validator;
        $this->entityFieldListFactory = $entityFieldListFactory;
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
                $queryList[] = $query;
            }
        }

        $queryList = ConflictResolver::resolveConflicts($queryList);

        dump($queryList);

        return $queryList;
    }

    protected function checkForQuery(string $queryFqcn, RequestParameterList $requestParameterList): ?QueryInterface
    {
        $query = new $queryFqcn();

        $requiredQueriableMethodList = $this->annotationHandler->listQueryRequiredMethods($queryFqcn);

        /** @var QueryProperty $queryProperty */
        foreach ($requiredQueriableMethodList as $queryProperty) {
            $this->valueAssigner->assignQueryPropertyValue($requestParameterList, $query, $queryProperty);
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
