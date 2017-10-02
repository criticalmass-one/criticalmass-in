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

        $menu->addChild('Anmelden', ['route' => 'fos_user_security_login', 'attributes' => ['foo' => 'bar']]);

        $menu->addChild('Benutzerkonto', ['route' => 'caldera_criticalmass_frontpage']);
        $menu['Benutzerkonto']->addChild('Dein Profil');
        $menu['Benutzerkonto']->addChild('Abmelden', ['route' => 'fos_user_security_logout']);

        return $menu;
    }
}

/**
 * {% if app.getUser() %}

<li class="dropdown">
<a href="{{ path('fos_user_security_login') }}" class="dropdown-toggle"
data-toggle="dropdown">
<i class="fa fa-user"></i>
{% trans %}criticalmass.navigation.account.account{% endtrans %}
<span class="caret"></span>
</a>

<ul class="dropdown-menu" role="menu">
<li>
<a href="{{ path('caldera_criticalmass_track_list') }}">
{% trans %}criticalmass.navigation.account.tracks{% endtrans %}
</a>
</li>
<li>
<a href="{{ path('caldera_criticalmass_photo_user_list') }}">
{% trans %}criticalmass.navigation.account.photos{% endtrans %}
</a>
</li>
<li class="divider"></li>
<li>
<a href="{{ path('fos_user_security_logout') }}">
{% trans %}criticalmass.navigation.account.logout{% endtrans %}
</a>
</li>
</ul>
</li>
{% else %}
<li>
<a href="#" data-toggle="modal" data-target="#loginModal">
<i class="fa fa-user"></i>
{% trans %}criticalmass.navigation.account.login{% endtrans %}
</a>
</li>
{% endif %}
 */