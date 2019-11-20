<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\DataQueryManager;

use App\Criticalmass\DataQuery\Factory\ParameterFactory\ParameterFactoryInterface;
use App\Criticalmass\DataQuery\Factory\QueryFactory\QueryFactoryInterface;
use App\Criticalmass\DataQuery\FinderFactory\FinderFactoryInterface;
use App\Criticalmass\DataQuery\RequestParameterList\RequestParameterList;

class DataQueryManager implements DataQueryManagerInterface
{
    /** @var QueryFactoryInterface $queryFactory */
    protected $queryFactory;

    /** @var ParameterFactoryInterface $parameterFactory */
    protected $parameterFactory;

    /** @var FinderFactoryInterface $finderFactory */
    protected $finderFactory;

    public function __construct(QueryFactoryInterface $queryFactory, ParameterFactoryInterface $parameterFactory, FinderFactoryInterface $finderFactory)
    {
        $this->queryFactory = $queryFactory;
        $this->parameterFactory = $parameterFactory;
        $this->finderFactory = $finderFactory;
    }
    
    public function query(RequestParameterList $requestParameterList, string $entityFqcn): array
    {
        $queryList = $this->queryFactory->setEntityFqcn($entityFqcn)->createFromList($requestParameterList);
        $parameterList = $this->parameterFactory->setEntityFqcn($entityFqcn)->createFromList($requestParameterList);

        $finder = $this->finderFactory->createFinderForFqcn($entityFqcn);

        return $finder->executeQuery($queryList, $parameterList);
    }
}
