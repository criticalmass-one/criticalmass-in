<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Home', array('route' => 'caldera_criticalmass_frontpage'));

        // access services from the container!
        $em = $this->container->get('doctrine')->getManager();
        // findMostRecent and Blog are just imaginary examples
        $blog = $em->getRepository('AppBundle:Ride')->findAll();

        $menu->addChild('Latest Blog Post', array(
            'route' => 'caldera_criticalmass_frontpage',
            'routeParameters' => [])
        );

        // create another menu item
        $menu->addChild('About Me', array('route' => 'caldera_criticalmass_frontpage'));
        // you can also add sub level's to your menu's as follows
        $menu['About Me']->addChild('Edit profile', array('route' => 'caldera_criticalmass_frontpage'));

        // ... add more children

        return $menu;
    }
}