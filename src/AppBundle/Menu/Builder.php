<?php declare(strict_types=1);

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class Builder
{
    /** @var FactoryInterface $factory */
    protected $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function mainMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $menu
            ->addChild('Critical Mass', ['uri' => '#', 'class' => 'dropdown'])
            ->setExtra('dropdown', true)
            ->setLinkAttribute('class', 'dropdown-toggle')
            ->setLinkAttribute('data-toggle', 'dropdown')
            ->setChildrenAttribute('class', 'dropdown-menu');

        $menu['Critical Mass']
            ->addChild('Über die Critical Mass', ['route' => 'caldera_criticalmass_help_about']);

        $menu['Critical Mass']
            ->addChild('Häufig gestellte Fragen', ['route' => 'caldera_criticalmass_help_faq']);

        $menu['Critical Mass']
            ->addChild('Hilfe', ['route' => 'caldera_criticalmass_help_index']);

        $menu['Critical Mass']
            ->addChild('Über criticalmass.in', ['route' => 'caldera_criticalmass_intro']);

        $menu
            ->addChild('Städte', ['uri' => '#'])
            ->setExtra('dropdown', true);

        $menu['Städte']
            ->addChild('Städteliste', ['route' => 'caldera_criticalmass_city_list']);

        $menu['Städte']
            ->addChild('Verzeichnis', ['route' => 'caldera_criticalmass_region_world']);

        $menu['Städte']
            ->addChild('Kalender', ['route' => 'caldera_criticalmass_calendar']);

        $menu->addChild('Statistik', ['uri' => '#'])
            ->setExtra('dropdown', true);

        $menu['Statistik']
            ->addChild('Übersicht', ['route' => 'caldera_criticalmass_statistic_overview']);

        $menu['Statistik']
            ->addChild('Top 10', ['route' => 'caldera_criticalmass_statistic_topten']);

        $menu['Statistik']
            ->addChild('Monatsauswertung', [
                'route' => 'caldera_criticalmass_statistic_ride_month',
                'routeParameters' => [
                    'year' => (new \DateTime())->format('Y'),
                    'month' => (new \DateTime())->format('m'),
                ]]);

        $menu->addChild('Community', ['uri' => '#'])
            ->setExtra('dropdown', true);

        $menu['Community']
            ->addChild('Timeline', ['route' => 'caldera_criticalmass_timeline_index']);

        $menu['Community']
            ->addChild('Diskussion', ['route' => 'caldera_criticalmass_board_overview']);

        $menu['Community']
            ->addChild('Fotos', ['route' => 'caldera_criticalmass_photo_examplegallery']);

        $menu->addChild('Anmelden', ['route' => 'fos_user_security_login'])->setLinkAttributes(['href' => '#', 'data-toggle' => 'modal', 'data-target' => '#loginModal']);

        $menu->addChild('Benutzerkonto', ['uri' => '#'])
            ->setExtra('dropdown', true);

        $menu['Benutzerkonto']->addChild('Dein Profil');
        $menu['Benutzerkonto']->addChild('Abmelden', ['route' => 'fos_user_security_logout']);

        return $menu;
    }
}
