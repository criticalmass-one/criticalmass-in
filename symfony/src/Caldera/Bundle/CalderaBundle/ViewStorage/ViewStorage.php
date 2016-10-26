<?php

namespace Caldera\Bundle\CalderaBundle\ViewStorage;

use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;
use Doctrine\Common\Cache\MemcachedCache;
use Memcached;

class ViewStorage implements ViewStorageInterface
{
    /** @var MemcachedCache $cache */
    protected $cache;

    public function __construct(MemcachedCache $cache)
    {
        $this->cache = $cache;
    }

    public function countView(ViewableInterface $viewable)
    {
        /** @var Memcached $cache */
        $cache = $this->cache->getMemcached();

        $viewStorage = $cache->get('view_storage');

        if (!$viewStorage) {
            $viewStorage = [];
        }

        $viewDateTime = new \DateTime('now', new \DateTimeZone('UTC'));

        $view =
            [
                'className' => $this->getClassName($viewable),
                'entityId' => $viewable->getId(),
                'userId' => ($this->getUser() ? $this->getUser()->getId() : null),
                'dateTime' => $viewDateTime->format('Y-m-d H:i:s')
            ];

        $viewStorage[] = $view;

        $cache->set('view_storage', $viewStorage);
    }

    protected function getClassName(ViewableInterface $viewable): string
    {
        $namespaceClass = get_class($viewable);
        $namespaceParts = explode('\\', $namespaceClass);

        $className = array_pop($namespaceParts);

        return $className;
    }
}