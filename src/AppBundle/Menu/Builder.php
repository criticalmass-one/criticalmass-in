<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options): ItemInterface
    {
        $menu = $factory->createItem('root');

        $menu->setChildrenAttribute('class', 'navbar-nav mr-auto');

        $menu->addChild('Critical Mass', ['route' => 'caldera_criticalmass_frontpage']);
        $menu['Critical Mass']->addChild('Über die Critical Mass');
        $menu['Critical Mass']->addChild('Häufig gestellte Fragen');
        $menu['Critical Mass']->addChild('Hilfe');

        $menu->addChild('Städte', ['route' => 'caldera_criticalmass_frontpage']);
        $menu['Städte']->addChild('Städteliste');
        $menu['Städte']->addChild('Verzeichnis');
        $menu['Städte']->addChild('Kalender');

        $menu->addChild('Statistik', ['route' => 'caldera_criticalmass_frontpage']);
        $menu['Statistik']->addChild('Übersicht');
        $menu['Statistik']->addChild('facebook-Statistiken');

        $menu->addChild('Community', ['route' => 'caldera_criticalmass_frontpage']);
        $menu['Community']->addChild('Timeline');
        $menu['Community']->addChild('Diskussion');
        $menu['Community']->addChild('Fotos');

        $menu->addChild('Benutzerkonto', ['route' => 'caldera_criticalmass_frontpage']);

        return $menu;
    }
}