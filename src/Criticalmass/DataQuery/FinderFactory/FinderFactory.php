<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\FinderFactory;

use App\Criticalmass\DataQuery\Finder\Finder;
use App\Criticalmass\DataQuery\Finder\FinderInterface;
use App\Criticalmass\Util\ClassUtil;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use Psr\Container\ContainerInterface;

class FinderFactory implements FinderFactoryInterface
{
    protected ContainerInterface $container;
    protected TransformedFinder $finder;
    protected RepositoryManagerInterface $repositoryManager;

    public function __construct(ContainerInterface $container, RepositoryManagerInterface $repositoryManager, TransformedFinder $finder)
    {
        $this->repositoryManager = $repositoryManager;
        $this->container = $container;
        $this->finder = $finder;
    }

    public function createFinderForFqcn(string $fqcn): FinderInterface
    {
        $className = ClassUtil::getLowercaseShortnameFromFqcn($fqcn);

        $schema = 'criticalmass_%s';

        $indexName = sprintf($schema, $className);

        if ($this->repositoryManager->hasRepository($indexName)) {
            $repository = $this->repositoryManager->getRepository($indexName);

            return new Finder($repository);
        }

        throw new \Exception(sprintf('Could not find repository for entity "%s"', $fqcn));
    }
}
