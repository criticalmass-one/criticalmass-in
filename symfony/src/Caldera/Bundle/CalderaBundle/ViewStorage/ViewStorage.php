<?php
/**
 * Created by PhpStorm.
 * User: maltehuebner
 * Date: 26.10.16
 * Time: 15:26
 */

namespace Caldera\Bundle\CalderaBundle\ViewStorage;


use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;
use Lsw\MemcacheBundle\Cache\LoggingMemcache;

class ViewStorage
{
    public function __construct( )
    {
    }

    protected function getClassName(ViewableInterface $viewable): string
    {
        $namespaceClass = get_class($viewable);
        $namespaceParts = explode('\\', $namespaceClass);

        $className = array_pop($namespaceParts);

        return $className;
    }

    public function countView(ViewableInterface $viewable)
    {
        /** @var LoggingMemcache $memcache */
        $memcache = $this->get('memcache.criticalmass');

        $viewStorage = $memcache->get('view_storage');

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

        $memcache->set('view_storage', $viewStorage);
    }
}