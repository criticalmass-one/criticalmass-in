<?php

namespace Caldera\CriticalmassStatisticBundle\Utility\StatisticEntityWriter;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\ParameterBag;

class StatisticEntityWriter
{
    private $controller;
    private $entity;

    public function __construct(Controller $controller, StatisticEntity $entity)
    {
        $this->controller = $controller;
        $this->entity = $entity;
    }

    public function execute()
    {
        $this->entity->setHost($_SERVER['HTTP_HOST']);
        $this->entity->setRemoteAddr($_SERVER['REMOTE_ADDR']);
        $this->entity->setAgent($_SERVER['HTTP_USER_AGENT']);
        $this->entity->setDateTime(new \DateTime());
        $this->entity->setRemoteHost(gethostbyaddr($_SERVER['REMOTE_ADDR']));
        $this->entity->setReferer(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
        $this->entity->setHost($_SERVER['SERVER_NAME']);
        $this->entity->setQuery($_SERVER['REQUEST_URI']);
        $this->entity->setEnvironment('dev');
        //$this->controller->container->get(‘kernel’)->getEnvironment());

        if ($this->controller->getRequest()->getSession()->has('city'))
        {
            $this->entity->setCity($this->controller->getRequest()->getSession()->get('city'));
        }

        if ($this->controller->getUser())
        {
            $this->entity->setUser($this->controller->getUser());
            $this->controller->get('fos_user.user_manager')->updateUser($this->controller->getUser());
        }

        return $this->entity;
    }
} 