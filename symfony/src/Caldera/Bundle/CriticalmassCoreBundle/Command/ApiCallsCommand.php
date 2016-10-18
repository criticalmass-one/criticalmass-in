<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\ApiCall;
use Caldera\Bundle\CalderaBundle\Entity\App;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ApiCallsCommand extends ContainerAwareCommand
{
    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * @var EntityManager $manager
     */
    protected $manager;

    protected $memcache;

    protected function configure()
    {
        $this
            ->setName('criticalmass:api:storecalls')
            ->setDescription('Store calls')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();
        $this->memcache = $this->getContainer()->get('memcache.criticalmass');

        $apps = $this->doctrine->getRepository('CalderaBundle:App')->findAll();

        /**
         * @var App $app
         */
        foreach ($apps as $app) {
            $apiCalls = $this->memcache->get('api_app'.$app->getId().'_calls');

            if ($apiCalls) {
                $output->writeln('App #'.$app->getId().': '.$apiCalls.' calls');

                for ($i = 1; $i <= $apiCalls; ++$i) {
                    $apiCallArray = json_decode($this->memcache->get('api_app'.$app->getId().'_call'.$i));

                    $callDateTime = new \DateTime();
                    $callDateTime->setTimestamp(round($apiCallArray->timestamp / 1000));
                    
                    $apiCall = new ApiCall();
                    $apiCall->setApp($app);
                    $apiCall->setReferer($apiCallArray->referer);
                    $apiCall->setRequest($apiCallArray->request);
                    $apiCall->setDateTime($callDateTime);

                    $this->manager->persist($apiCall);

                    $this->memcache->delete('api_app'.$app->getId().'_call'.$i);
                }

                $app->setApiCalls($app->getApiCalls() + $apiCalls);

                $this->manager->merge($app);

                $this->memcache->delete('api_app'.$app->getId().'_calls');
            }
        }

        $this->manager->flush();
    }
}