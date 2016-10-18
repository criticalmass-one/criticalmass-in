<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyRideLocationCommand extends ContainerAwareCommand
{
    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * @var EntityManager $manager
     */
    protected $manager;

    protected $messageBird;

    protected function configure()
    {
        $this
            ->setName('criticalmass:notify:ridelocation')
            ->setDescription('Store calls');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();

        $this->messageBird = new \MessageBird\Client($this->getContainer()->getParameter('messagebird.api_key')); // Set your own API access key here.
        $this->notifyUser();
    }

    protected function notifyUser()
    {
        $message = new \MessageBird\Objects\Message();
        $message->originator = '004915117277032';
        $message->recipients = ['004915117277032'];
        $message->body = 'This is a test message with a smiling emoji 😀.';
        $message->datacoding = 'unicode';
        try {
            $MessageResult = $this->messageBird->messages->create($message);
            //var_dump($MessageResult);
        } catch (\MessageBird\Exceptions\AuthenticateException $e) {
            // That means that your accessKey is unknown
            echo 'wrong login';
        } catch (\MessageBird\Exceptions\BalanceException $e) {
            // That means that you are out of credits, so do something about it.
            echo 'no balance';
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}