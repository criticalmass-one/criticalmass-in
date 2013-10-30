<?php

namespace Caldera\CriticalmassCoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Caldera\CriticalmassCoreBundle\Entity\City;

class LoadCityData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		// Critical Mass Augsburg
		$city = new City();
		$city->setCity("Augsburg");
		$city->setTitle("Critical Mass Augsburg");
		$city->setUrl("http://cmaugsburg.wordpress.com/");
		$city->setFacebook("https://www.facebook.com/criticalmassaugsburg");
		$city->setTwitter("");
		$city->setLatitude(48.371667);
		$city->setLongitude(10.898333);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-augsburg", $city);

		// Critical Mass Berlin
		$city = new City();
		$city->setCity("Berlin");
		$city->setTitle("Critical Mass Berlin");
		$city->setUrl("http://cmberlin.blogsport.de/");
		$city->setFacebook("https://www.facebook.com/pages/Critical-Mass-Berlin");
		$city->setTwitter("https://twitter.com/CM_Berlin");
		$city->setLatitude(52.518611);
		$city->setLongitude(13.408056);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-berlin", $city);

		// Critical Mass Bochum
		$city = new City();
		$city->setCity("Bochum");
		$city->setTitle("Critical Mass Bochum");
		$city->setUrl("");
		$city->setFacebook("https://www.facebook.com/pages/Critical-Mass-Bochum/154648004580049");
		$city->setTwitter("");
		$city->setLatitude(51.4825);
		$city->setLongitude(7.216944);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-bochum", $city);

		// Critical Mass Braunschweig
		$city = new City();
		$city->setCity("Braunschweig");
		$city->setTitle("Critical Mass Braunschweig");
		$city->setUrl("http://criticalmassbraunschweig.de.tl");
		$city->setFacebook("https://www.facebook.com/critical.mass.braunschweig");
		$city->setTwitter("");
		$city->setLatitude(52.269167);
		$city->setLongitude(10.521111);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-braunschweig", $city);

		// Critical Mass Bremen
		$city = new City();
		$city->setCity("Bremen");
		$city->setTitle("Critical Mass Bremen");
		$city->setUrl("http://www.criticalmass-bremen.de/");
		$city->setFacebook("https://www.facebook.com/CriticalMassBremen");
		$city->setTwitter("");
		$city->setLatitude(53.075878);
		$city->setLongitude(8.807311);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-bremen", $city);

		// Critical Mass Dortmund
		$city = new City();
		$city->setCity("Dortmund");
		$city->setTitle("Critical Mass Dortmund");
		$city->setUrl("http://velolove.me/cmdo/");
		$city->setFacebook("https://www.facebook.com/pages/Critical-Mass-Dortmund/161748607183306");
		$city->setTwitter("");
		$city->setLatitude(51.514167);
		$city->setLongitude(7.463889);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-dortmund", $city);

		// Critical Mass Dresden
		$city = new City();
		$city->setCity("Dresden");
		$city->setTitle("Critical Mass Dresden");
		$city->setUrl("http://www.myspace.com/dresdencriticalmass");
		$city->setFacebook("https://www.facebook.com/criticalmassdresden");
		$city->setTwitter("");
		$city->setLatitude(51.049259);
		$city->setLongitude(13.73836);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-dresden", $city);

		// Critical Mass Duisburg
		$city = new City();
		$city->setCity("Duisburg");
		$city->setTitle("Critical Mass Duisburg");
		$city->setUrl("http://criticalmassduisburg.blogsport.de/");
		$city->setFacebook("https://www.facebook.com/CriticalMassDuisburg");
		$city->setTwitter("");
		$city->setLatitude(51.435147);
		$city->setLongitude(6.762692);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-duisburg", $city);

		// Critical Mass Duesseldorf
		$city = new City();
		$city->setCity("Düsseldorf");
		$city->setTitle("Critical Mass Düsseldorf");
		$city->setUrl("http://criticalmassduesseldorf.blogsport.de/");
		$city->setFacebook("");
		$city->setTwitter("");
		$city->setLatitude(51.225556);
		$city->setLongitude(6.782778);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-duesseldorf", $city);

		// Critical Mass Essen
		$city = new City();
		$city->setCity("Essen");
		$city->setTitle("Critical Mass Essen");
		$city->setUrl("");
		$city->setFacebook("https://www.facebook.com/critical.mass.essen");
		$city->setTwitter("");
		$city->setLatitude(51.458069);
		$city->setLongitude(7.014761);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-essen", $city);

		// Critical Mass Flensburg
		$city = new City();
		$city->setCity("Flensburg");
		$city->setTitle("Critical Mass Flensburg");
		$city->setUrl("http://criticalmassflensburg.blogsport.de/");
		$city->setFacebook("https://www.facebook.com/groups/148455028667984/");
		$city->setTwitter("");
		$city->setLatitude(54.781944);
		$city->setLongitude(9.436667);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-flensburg", $city);

		// Critical Mass Frankfurt
		$city = new City();
		$city->setCity("Frankfurt");
		$city->setTitle("Critical Mass Frankfurt");
		$city->setUrl("http://www.critical-mass-frankfurt.de/");
		$city->setFacebook("https://www.facebook.com/pages/Critical-Mass-Frankfurt-am-Main/151299114891239");
		$city->setTwitter("");
		$city->setLatitude(50.110556);
		$city->setLongitude(8.682222);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-frankfurt", $city);

		// Critical Mass Freiburg
		$city = new City();
		$city->setCity("Freiburg");
		$city->setTitle("Critical Mass Freiburg");
		$city->setUrl("http://www.critical-mass-freiburg.de");
		$city->setFacebook("https://www.facebook.com/critical.mass.freiburg");
		$city->setTwitter("");
		$city->setLatitude(47.994828);
		$city->setLongitude(7.849881);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-freiburg", $city);

        // Critical Mass Hamburg
        $city = new City();
        $city->setCity("Hamburg");
        $city->setTitle("Critical Mass Hamburg");
        $city->setDescription("Mit 3.252 Teilnehmern im Sommer 2013 ist die Critical Mass Hamburg die größte monatliche Critical Mass der Welt.");
        $city->setUrl("http://www.criticalmass-hamburg.de/");
        $city->setFacebook("https://www.facebook.com/criticalmasshamburg");
        $city->setTwitter("https://www.twitter.com/cm_hh");
        $city->setLatitude(53.550556);
        $city->setLongitude(9.993333);

        $manager->persist($city);
        $manager->flush();

        $this->addReference("city-hamburg", $city);

        // Critical Mass Hamburg-Altona
        $city = new City();
        $city->setCity("Altona");
        $city->setTitle("Critical Mass Altona");
        $city->setUrl("http://www.critical-mass-altona.de/");
        $city->setFacebook("https://www.facebook.com/CriticalMassAltona");
        $city->setTwitter("https://www.twitter.com/cm_altona‎");
        $city->setLatitude(53.55);
        $city->setLongitude(9.933333);

        $manager->persist($city);
        $manager->flush();

        $this->addReference("city-altona", $city);

		// Critical Mass Hannover
		$city = new City();
		$city->setCity("Hannover");
		$city->setTitle("Critical Mass Hannover");
		$city->setUrl("http://criticalmasshannover.wordpress.com/");
		$city->setFacebook("https://www.facebook.com/pages/Critical-Mass-Hannover/483028381718877");
		$city->setTwitter("");
		$city->setLatitude(52.374444);
		$city->setLongitude(9.738611);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-hannover", $city);

		// Critical Mass Kassel
		$city = new City();
		$city->setCity("Kassel");
		$city->setTitle("Critical Mass Kassel");
		$city->setUrl("http://www.myspace.com/criticalmasskassel");
		$city->setFacebook("");
		$city->setTwitter("");
		$city->setLatitude(51.316667);
		$city->setLongitude(9.5);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-kassel", $city);

		// Critical Mass Kiel
		$city = new City();
		$city->setCity("Kiel");
		$city->setTitle("Critical Mass Kiel");
		$city->setUrl("http://criticalmasskiel.wordpress.com/");
		$city->setFacebook("https://www.facebook.com/CriticalMassKiel");
		$city->setTwitter("");
		$city->setLatitude(54.325278);
		$city->setLongitude(10.140556);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-kiel", $city);

		// Critical Mass Koblenz
		$city = new City();
		$city->setCity("Koblenz");
		$city->setTitle("Critical Mass Koblenz");
		$city->setUrl("http://www.criticalmasskoblenz.blogspot.de/");
		$city->setFacebook("https://www.facebook.com/criticalmass.koblenz");
		$city->setTwitter("");
		$city->setLatitude(50.356667);
		$city->setLongitude(7.593889);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-koblenz", $city);

		// Critical Mass Koeln
		$city = new City();
		$city->setCity("Cologne");
		$city->setTitle("Critical Mass Koeln");
		$city->setUrl("http://www.critical-mass-cologne.de/");
		$city->setFacebook("https://www.facebook.com/critical.mass.koeln");
		$city->setTwitter("");
		$city->setLatitude(50.938056);
		$city->setLongitude(6.956944);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-koeln", $city);

		// Critical Mass Leipzig
		$city = new City();
		$city->setCity("Leipzig");
		$city->setTitle("Critical Mass Leipzig");
		$city->setUrl("http://criticalmass.wikia.com/wiki/Leipzig");
		$city->setFacebook("");
		$city->setTwitter("https://twitter.com/cmleipzig");
		$city->setLatitude(51.340333);
		$city->setLongitude(12.37475);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-leipzig", $city);

		// Critical Mass Lübeck
		$city = new City();
		$city->setCity("Lübeck");
		$city->setTitle("Critical Mass Lübeck");
		$city->setUrl("");
		$city->setFacebook("https://www.facebook.com/CriticalMassLubeck");
		$city->setTwitter("");
		$city->setLatitude(53.869722);
		$city->setLongitude(10.686389);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-luebeck", $city);

		// Critical Mass Magdeburg
		$city = new City();
		$city->setCity("Magdeburg");
		$city->setTitle("Critical Mass Magdeburg");
		$city->setUrl("");
		$city->setFacebook("https://www.facebook.com/criticalmass.magdeburg");
		$city->setTwitter("");
		$city->setLatitude(52.133333);
		$city->setLongitude(11.616667);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-magdeburg", $city);

        // Critical Mass Mannheim
        $city = new City();
        $city->setCity("Mannheim");
        $city->setTitle("Critical Mass Mannheim");
        $city->setUrl("http://criticalmassmannheim.blogspot.de/");
        $city->setFacebook("https://www.facebook.com/pages/Critical-mass-bike-flash-mob-Mannheim/165766566835816");
        $city->setTwitter("");
        $city->setLatitude(49.483611);
        $city->setLongitude(8.463056);

        $manager->persist($city);
        $manager->flush();

        $this->addReference("city-mannheim", $city);

        // Critical Mass München
        $city = new City();
        $city->setCity("München");
        $city->setTitle("Critical Mass München");
        $city->setUrl("");
        $city->setFacebook("https://www.facebook.com/criticalmassmuenchen");
        $city->setTwitter("");
        $city->setLatitude(48.137222);
        $city->setLongitude(11.575556);

        $manager->persist($city);
        $manager->flush();

        $this->addReference("city-muenchen", $city);

		// Critical Mass Nürnberg
		$city = new City();
		$city->setCity("Nürnberg");
		$city->setTitle("Critical Mass Nürnberg");
		$city->setUrl("http://www.myspace.com/critical_mass_nuernberg");
		$city->setFacebook("");
		$city->setTwitter("");
		$city->setLatitude(49.452778);
		$city->setLongitude(11.077778);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-nuernberg", $city);

		// Critical Mass Oldenburg
		$city = new City();
		$city->setCity("Oldenburg");
		$city->setTitle("Critical Mass Oldenburg");
		$city->setUrl("http://criticalmassoldenburg.blogsport.de/");
		$city->setFacebook("https://www.facebook.com/CriticalMassOldenburg");
		$city->setTwitter("");
		$city->setLatitude(53.143889);
		$city->setLongitude(8.213889);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-oldenburg", $city);

		// Critical Mass Rostock
		$city = new City();
		$city->setCity("Rostock");
		$city->setTitle("Critical Mass Rostock");
		$city->setUrl("http://www.cmrostock.de/");
		$city->setFacebook("https://www.facebook.com/CriticalMassRostock");
		$city->setTwitter("");
		$city->setLatitude(54.083333);
		$city->setLongitude(12.133333);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-rostock", $city);

		// Critical Mass Stuttgart
		$city = new City();
		$city->setCity("Stuttgart");
		$city->setTitle("Critical Mass Stuttgart");
		$city->setUrl("http://criticalmassstuttgart.wordpress.com/");
		$city->setFacebook("https://www.facebook.com/getonyourbike");
		$city->setTwitter("");
		$city->setLatitude(48.775556);
		$city->setLongitude(9.182778);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-stuttgart", $city);

        // Critical Mass Trier
        $city = new City();
        $city->setCity("Trier");
        $city->setTitle("Critical Mass Trier");
        $city->setUrl("");
        $city->setFacebook("");
        $city->setTwitter("https://twitter.com/VelomobTrier");
        $city->setLatitude(49.7596);
        $city->setLongitude(6.6439);

        $manager->persist($city);
        $manager->flush();

        $this->addReference("city-trier", $city);

        // Critical Mass Wedel
        $city = new City();
        $city->setCity("Wedel");
        $city->setTitle("Lichtertour Wedel");
        $city->setUrl("http://www.adfc-wedel.de/");
        $city->setFacebook("");
        $city->setTwitter("");
        $city->setLatitude(53.5810);
        $city->setLongitude(9.7037);

        $manager->persist($city);
        $manager->flush();

        $this->addReference("city-wedel", $city);

		// Critical Mass Wuppertal
		$city = new City();
		$city->setCity("Wuppertal");
		$city->setTitle("Critical Mass Wuppertal");
		$city->setUrl("http://cmwpt.wordpress.com/");
		$city->setFacebook("https://www.facebook.com/CriticalMassWuppertal");
		$city->setTwitter("");
		$city->setLatitude(51.259167);
		$city->setLongitude(7.211111);

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-wuppertal", $city);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return 1;
	}
}
