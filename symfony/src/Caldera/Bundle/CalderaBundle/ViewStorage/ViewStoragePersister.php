<?php

namespace Caldera\Bundle\CalderaBundle\ViewStorage;

use Caldera\Bundle\CalderaBundle\Entity\User;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewInterface;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\ORM\EntityManager;
use Memcached;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ViewStoragePersister
{
    protected $doctrine;

    /** @var EntityManager $manager */
    protected $manager;

    /** @var TokenStorageInterface $tokenStorage */
    protected $tokenStorage;

    /** @var OutputInterface $output */
    protected $output = null;

    public function __construct(MemcachedCache $cache, $doctrine)
    {
        $this->cache = $cache;
        $this->doctrine = $doctrine;
        $this->manager = $doctrine->getManager();
    }

    public function setOutput(OutputInterface $output): ViewStoragePersister
    {
        $this->output = $output;

        return $this;
    }

    public function persistViews()
    {
        /** @var Memcached $cache */
        $cache = $this->cache->getMemcached();

        $viewStorage = $cache->get('view_storage');
        //$cache->delete('view_storage');

        if (!$viewStorage || !is_array($viewStorage) || !count($viewStorage)) {
            return;
        }

        foreach ($viewStorage as $view) {
            $this->storeView($view);
        }
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
        $viewClassName = 'Caldera\Bundle\CalderaBundle\Entity\\' . $className . 'View';

        $view = new $viewClassName;

        return $view;
    }

    protected function getUser(int $userId): User
    {
        $user = $this->manager->getRepository('CalderaBundle:User')->find($userId);

        return $user;
    }

    protected function getEntity(string $className, int $entityId): ViewableInterface
    {
        $entity = $this->manager->getRepository('CalderaBundle:' . $className)->find($entityId);

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

