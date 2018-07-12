<?php

namespace App\Criticalmass\ViewStorage;

use App\Entity\User;
use App\EntityInterface\ViewableInterface;
use App\EntityInterface\ViewInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Console\Output\OutputInterface;

class ViewStoragePersister implements ViewStoragePersisterInterface
{
    /** @var RegistryInterface $doctrine */
    protected $doctrine;

    /** @var EntityManager $manager */
    protected $manager;

    /** @var OutputInterface $output */
    protected $output = null;

    /** @var AbstractAdapter $cache */
    protected $cache = null;

    public function __construct(RegistryInterface $doctrine)
    {
        $redisConnection = RedisAdapter::createConnection('redis://localhost');

        $this->cache = new RedisAdapter(
            $redisConnection,
            $namespace = '',
            $defaultLifetime = 0
        );

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
        $viewStorageItem = $this->cache->getItem('criticalmass-view_storage');

        if ($viewStorageItem->isHit()) {
            $viewArrayList = $viewStorageItem->get();

            foreach ($viewArrayList as $viewArray) {
                $this->storeView($viewArray);
            }
        }

        $this->cache->deleteItem('criticalmass-view_storage');

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
        $viewClassName = 'App\Entity\\' . $className . 'View';

        $view = new $viewClassName;

        return $view;
    }

    protected function getUser(int $userId): User
    {
        $user = $this->manager->getRepository('App:User')->find($userId);

        return $user;
    }

    protected function getEntity(string $className, int $entityId): ViewableInterface
    {
        $entity = $this->manager->getRepository('App:' . $className)->find($entityId);

        return $entity;
    }

    protected function log(string $message): ViewStoragePersister
    {
        if ($this->output) {
            $this->output->writeln($message);
        } else {
            echo $message . "\n";
        }

        return $this;
    }
}

