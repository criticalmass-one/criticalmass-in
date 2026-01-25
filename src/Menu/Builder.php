<?php declare(strict_types=1);

namespace App\Menu;

use App\Entity\User;
use Knp\Menu\ItemInterface;

class Builder extends AbstractBuilder
{
    public function mainMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $menu
            ->addChild('Critical Mass', ['uri' => '#', 'class' => 'dropdown'])
            ->setExtra('dropdown', true);

        $menu['Critical Mass']
            ->addChild('Über die Critical Mass', ['uri' => 'https://criticalmass.blog/critical-mass/was-ist-die-critical-mass/']);

        $menu['Critical Mass']
            ->addChild('Häufig gestellte Fragen', ['uri' => 'https://criticalmass.blog/critical-mass/haeufig-gestellte-fragen-zur-critical-mass/']);

        $menu['Critical Mass']
            ->addChild('Hilfe', ['uri' => 'https://criticalmass.blog/criticalmass-one/hilfe/']);

        $menu['Critical Mass']
            ->addChild('Über criticalmass.in', ['uri' => 'https://criticalmass.blog/criticalmass-one/ueber-criticalmass-in/']);

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

        if ($this->featureManager->isActive('photos')) {
            $menu['Community']
                ->addChild('Fotos', ['route' => 'caldera_criticalmass_photo_examplegallery']);
        }

        if ($this->isUserLoggedIn()) {
            $menu->addChild('Benutzerkonto', ['uri' => '#'])
                ->setExtra('dropdown', true);

            $menu['Benutzerkonto']->addChild('Dein Profil', ['route' => 'criticalmass_user_usermanagement']);

            $menu['Benutzerkonto']->addChild('divider1', ['attributes' => ['class' => 'dropdown-divider-wrapper']]);

            $menu['Benutzerkonto']->addChild('Profilfoto ändern', ['route' => 'criticalmass_user_profile_photo']);
            $menu['Benutzerkonto']->addChild('Profilfarbe wählen', ['route' => 'criticalmass_user_profile_color']);
            $menu['Benutzerkonto']->addChild('Tourübersicht', ['route' => 'criticalmass_user_participation_list']);
            $menu['Benutzerkonto']->addChild('Deine Tracks', ['route' => 'caldera_criticalmass_track_list']);
            $menu['Benutzerkonto']->addChild('Deine Fotos', ['route' => 'caldera_criticalmass_photo_user_list']);

            $menu['Benutzerkonto']->addChild('divider2', ['attributes' => ['class' => 'dropdown-divider-wrapper']]);

            $menu['Benutzerkonto']
                ->addChild('Abmelden', ['route' => 'logout']);
        } else {
            $menu
                ->addChild('Anmelden', ['route' => 'login']);
        }


        return $menu;
    }
}
