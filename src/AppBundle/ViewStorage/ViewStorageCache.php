<?php

namespace AppBundle\ViewStorage;

use AppBundle\Entity\User;
use AppBundle\EntityInterface\ViewableInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ViewStorageCache implements ViewStorageCacheInterface
{
    /** @var FilesystemAdapter $cache */
    protected $cache;

    /** @var TokenStorageInterface $tokenStorage */
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->cache = new FilesystemAdapter();
        $this->tokenStorage = $tokenStorage;
    }

    public function countView(ViewableInterface $viewable)
    {
        $viewStorageItem = $this->cache->getItem('view_storage');

        if (!$viewStorageItem->isHit()) {
            $viewStorage = [];
        } else {
            $viewStorage = $viewStorageItem->get();
        }

        $viewDateTime = new \DateTime('now', new \DateTimeZone('UTC'));

        $user = $this->tokenStorage->getToken()->getUser();
        $userId = null;

        if ($user instanceof User) {
            $userId = $user->getId();
        }

        $view =
            [
                'className' => $this->getClassName($viewable),
                'entityId' => $viewable->getId(),
                'userId' => $userId,
                'dateTime' => $viewDateTime->format('Y-m-d H:i:s')
            ];

        $viewStorage[] = $view;
        $viewStorageItem->set($viewStorage);

        $this->cache->save($viewStorageItem);
    }

    protected function getClassName(ViewableInterface $viewable): string
    {
        $namespaceClass = get_class($viewable);
        $namespaceParts = explode('\\', $namespaceClass);

        $className = array_pop($namespaceParts);

        return $className;
    }
}