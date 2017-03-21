<?php

namespace AppBundle\ViewStorage;

use AppBundle\Entity\User;
use AppBundle\EntityInterface\ViewableInterface;
use AppBundle\EntityInterface\ViewInterface;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\ORM\EntityManager;
use Memcached;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ViewStoragePersister implements ViewStoragePersisterInterface
{
    protected $doctrine;

    /** @var EntityManager $manager */
    protected $manager;

    /** @var TokenStorageInterface $tokenStorage */
    protected $tokenStorage;

    /** @var OutputInterface $output */
    protected $output = null;

    protected $cache = null;

    public function __construct($doctrine)
    {
        $this->cache = new FilesystemAdapter();
        $this->doctrine = $doctrine;
        $this->manager = $doctrine->getManager();
    }

    public function setOutput(OutputInterface $output): ViewStoragePersisterInterface
    {
        $this->output = $output;

        return $this;
    }

    public function persistViews(): ViewStoragePersisterInterface
    {
        $viewStorageItem = $this->cache->getItem('view_storage');

        if ($viewStorageItem->isHit()) {
            $viewArrayList = $viewStorageItem->get();

            foreach ($viewArrayList as $viewArray) {
                $this->storeView($viewArray);
            }
        }

        $this->cache->deleteItem('view_storage');

        $this->manager->flush();

        return $this;
    }

    protected function storeView(array $viewArray)
    {
        $view = $this->getView($viewArray['className']);
        $entity = $this->getEntity($viewArray['className'], $viewArray['entityId']);
        $viewSetEntityMethod = 'set' . $viewArray['className'];

        $view->$viewSetEntityMethod($entity);

        $userId = $viewArray['userId'];
        $user = null;

        if (is_int($userId)) {
            $user = $this->getUser($userId);
        }

        $view->setUser($user);

        $dateTime = new \DateTime($viewArray['dateTime']);
        $view->setDateTime($dateTime);

        $entity->incViews();

        $this->manager->persist($view);
        $this->manager->persist($entity);

        $this->log(sprintf(
            'Saved view for <comment>%s</comment> <info>#%d</info> (%s)',
            $viewArray['className'],
            $viewArray['entityId'],
            $dateTime->format('Y-m-d H:i:s')
        ));
    }

    protected function getView(string $className): ViewInterface
    {
        $viewClassName = 'AppBundle\Entity\\' . $className . 'View';

        $view = new $viewClassName;

        return $view;
    }

    protected function getUser(int $userId): User
    {
        $user = $this->manager->getRepository('AppBundle:User')->find($userId);

        return $user;
    }

    protected function getEntity(string $className, int $entityId): ViewableInterface
    {
        $entity = $this->manager->getRepository('AppBundle:' . $className)->find($entityId);

        return $entity;
    }

    protected function log(string $message): ViewStoragePersister
    {
        if ($this->output) {
            $this->output->writeln($message);
        } else {
            echo $message."\n";
        }

        return $this;
    }
}

