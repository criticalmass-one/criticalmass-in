<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\FinderFactory;

use App\Criticalmass\DataQuery\Finder\Finder;
use App\Criticalmass\DataQuery\Finder\FinderInterface;
use App\Criticalmass\Util\ClassUtil;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FinderFactory implements FinderFactoryInterface
{
    /** @var ContainerInterface $container */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function createFinderForFqcn(string $fqcn): FinderInterface
    {
        $className = ClassUtil::getLowercaseShortnameFromFqcn($fqcn);

        $schema = 'fos_elastica.finder.criticalmass_%s.%s';

        $finderServiceName = sprintf($schema, $className, $className);

        if ($this->container->has($finderServiceName)) {
            $fosFinder = $this->container->get($finderServiceName);

            return new Finder($fosFinder);
        }

        throw new \Exception(sprintf('Could not find service %s for entity fqcn %s', $finderServiceName, $fqcn));
    }
}
