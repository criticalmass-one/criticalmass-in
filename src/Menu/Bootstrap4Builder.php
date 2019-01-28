<?php declare(strict_types=1);

namespace App\Menu;

use App\Entity\User;
use Knp\Menu\ItemInterface;

class Bootstrap4Builder extends AbstractBuilder
{
    public function mainMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav navbar-nav mr-auto');

        $criticalMassDropdown = $menu->addChild('Critical Mass', [
            'attributes' => [
                'dropdown' => true,
            ],
        ]);

        $criticalMassDropdown->addChild('Über die Critical Mass', ['route' => 'caldera_criticalmass_help_about']);
        $criticalMassDropdown->addChild('Häufig gestellte Fragen', ['route' => 'caldera_criticalmass_help_faq']);
        $criticalMassDropdown->addChild('Hilfe', ['route' => 'caldera_criticalmass_help_index']);
        $criticalMassDropdown->addChild('Über criticalmass.in', ['route' => 'caldera_criticalmass_intro']);

        $cityDropdown = $menu->addChild('Städte', [
            'attributes' => [
                'dropdown' => true,
            ]
        ]);

        $cityDropdown->addChild('Städteliste', ['route' => 'caldera_criticalmass_city_list']);
        $cityDropdown->addChild('Verzeichnis', ['route' => 'caldera_criticalmass_region_world']);
        $cityDropdown->addChild('Kalender', ['route' => 'caldera_criticalmass_calendar']);


        $statisticDropdown = $menu->addChild('Statistik', [
            'attributes' => [
                'dropdown' => true
            ]
        ]);

        $statisticDropdown->addChild('Übersicht', ['route' => 'caldera_criticalmass_statistic_overview']);
        $statisticDropdown->addChild('Top 10', ['route' => 'caldera_criticalmass_statistic_topten']);
        $statisticDropdown->addChild('Monatsauswertung', [
            'route' => 'caldera_criticalmass_statistic_ride_month',
            'routeParameters' => [
                'year' => (new \DateTime())->format('Y'),
                'month' => (new \DateTime())->format('m'),
            ]]);

        $communityDropdown = $menu->addChild('Community', [
            'attributes' => [
                'dropdown' => true,
            ]
        ]);

        $communityDropdown->addChild('Timeline', ['route' => 'caldera_criticalmass_timeline_index']);
        $communityDropdown->addChild('Diskussion', ['route' => 'caldera_criticalmass_board_overview']);
        $communityDropdown->addChild('Fotos', ['route' => 'caldera_criticalmass_photo_examplegallery']);



        /*
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav navbar-nav');





        if ($this->isUserLoggedIn()) {
            $menu->addChild('Benutzerkonto', ['uri' => '#'])
                ->setExtra('dropdown', true);

            $menu['Benutzerkonto'
                ]->addChild('Dein Profil', ['route' => 'criticalmass_user_usermanagement']);

            $menu['Benutzerkonto']
                ->addChild('Abmelden', ['route' => 'fos_user_security_logout']);
        } else {
            $menu
                ->addChild('Anmelden', [
                    'uri' => '#'
                ])
                ->setLinkAttributes([
                    'data-toggle' => 'modal',
                    'data-target' => '#loginModal'
                ]);
        }


        return $menu;*/

        return $menu;
    }
}
