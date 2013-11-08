<?php

namespace Caldera\CriticalmassStatisticBundle\Listener;

use Caldera\CriticalmassStatisticBundle\Entity\StatisticVisit;
use Caldera\CriticalmassStatisticBundle\Utility\StatisticEntityWriter\StatisticEntityWriter;
use Caldera\CriticalmassStatisticBundle\Utility\Trackable;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent as FilterControllerEvent;

class StatisticListener
{
	protected $doctrine;

	public function __construct(Doctrine $doctrine)
	{
		$this->doctrine = $doctrine;
	}

	public function onKernelController(FilterControllerEvent $event)
	{
		$controllers = $event->getController();
		$controller = $controllers[0];

        if ($controller instanceof Trackable)
        {
            $visit = new StatisticVisit();

            $sew = new StatisticEntityWriter($controller, $visit);
            $visit = $sew->execute();

            $manager = $this->doctrine->getManager();
            $manager->persist($visit);
            $manager->flush();
        }
	}
}