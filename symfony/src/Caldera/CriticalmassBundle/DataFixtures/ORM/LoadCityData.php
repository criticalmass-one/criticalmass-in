<?php

namespace Caldera\CriticalmassBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Caldera\CriticalmassBundle\Entity\City;

class LoadCityData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		// Critical Mass Augsburg
		$city = new City();
		$city->setCity("augsburg");
		$city->setTitle("Critical Mass Augsburg");
		$city->setUrl("http://cmaugsburg.wordpress.com/");
		$city->setFacebook("https://www.facebook.com/criticalmassaugsburg");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-augsburg", $city);

		// Critical Mass Berlin
		$city = new City();
		$city->setCity("berlin");
		$city->setTitle("Critical Mass Berlin");
		$city->setUrl("http://cmberlin.blogsport.de/");
		$city->setFacebook("https://www.facebook.com/pages/Critical-Mass-Berlin");
		$city->setTwitter("https://twitter.com/CM_Berlin");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-berlin", $city);

		// Critical Mass Bochum
		$city = new City();
		$city->setCity("bochum");
		$city->setTitle("Critical Mass Bochum");
		$city->setUrl("");
		$city->setFacebook("https://www.facebook.com/pages/Critical-Mass-Bochum/154648004580049");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-bochum", $city);

		// Critical Mass Braunschweig
		$city = new City();
		$city->setCity("braunschweig");
		$city->setTitle("Critical Mass Braunschweig");
		$city->setUrl("http://criticalmassbraunschweig.de.tl");
		$city->setFacebook("https://www.facebook.com/critical.mass.braunschweig");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-braunschweig", $city);

		// Critical Mass Bremen
		$city = new City();
		$city->setCity("bremen");
		$city->setTitle("Critical Mass Bremen");
		$city->setUrl("http://www.criticalmass-bremen.de/");
		$city->setFacebook("https://www.facebook.com/CriticalMassBremen");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-bremen", $city);

		// Critical Mass Dortmund
		$city = new City();
		$city->setCity("dortmund");
		$city->setTitle("Critical Mass Dortmund");
		$city->setUrl("http://velolove.me/cmdo/");
		$city->setFacebook("https://www.facebook.com/pages/Critical-Mass-Dortmund/161748607183306");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-dortmund", $city);

		// Critical Mass Dresden
		$city = new City();
		$city->setCity("dresden");
		$city->setTitle("Critical Mass Dresden");
		$city->setUrl("http://www.myspace.com/dresdencriticalmass");
		$city->setFacebook("https://www.facebook.com/criticalmassdresden");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-dresden", $city);

		// Critical Mass Duisburg
		$city = new City();
		$city->setCity("duisburg");
		$city->setTitle("Critical Mass Duisburg");
		$city->setUrl("http://criticalmassduisburg.blogsport.de/");
		$city->setFacebook("https://www.facebook.com/CriticalMassDuisburg");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-duisburg", $city);

		// Critical Mass Duesseldorf
		$city = new City();
		$city->setCity("duesseldorf");
		$city->setTitle("Critical Mass Düsseldorf");
		$city->setUrl("http://criticalmassduesseldorf.blogsport.de/");
		$city->setFacebook("");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-duesseldorf", $city);

		// Critical Mass Essen
		$city = new City();
		$city->setCity("essen");
		$city->setTitle("Critical Mass Essen");
		$city->setUrl("");
		$city->setFacebook("https://www.facebook.com/critical.mass.essen");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-essen", $city);

		// Critical Mass Flensburg
		$city = new City();
		$city->setCity("flensburg");
		$city->setTitle("Critical Mass Flensburg");
		$city->setUrl("http://criticalmassflensburg.blogsport.de/");
		$city->setFacebook("https://www.facebook.com/groups/148455028667984/");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-flensburg", $city);

		// Critical Mass Frankfurt
		$city = new City();
		$city->setCity("frankfurt");
		$city->setTitle("Critical Mass Frankfurt");
		$city->setUrl("http://www.critical-mass-frankfurt.de/");
		$city->setFacebook("https://www.facebook.com/pages/Critical-Mass-Frankfurt-am-Main/151299114891239");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-frankfurt", $city);

		// Critical Mass Freiburg
		$city = new City();
		$city->setCity("freiburg");
		$city->setTitle("Critical Mass Freiburg");
		$city->setUrl("http://www.critical-mass-freiburg.de");
		$city->setFacebook("https://www.facebook.com/critical.mass.freiburg");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-freiburg", $city);

		// Critical Mass Hamburg
		$city = new City();
		$city->setCity("hamburg");
		$city->setTitle("Critical Mass Hamburg");
		$city->setUrl("http://www.criticalmass-hamburg.de/");
		$city->setFacebook("https://www.facebook.com/criticalmasshamburg");
		$city->setTwitter("https://www.twitter.com/cm_hh");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-hamburg", $city);

		// Critical Mass Hamburg-Altona
		$city = new City();
		$city->setCity("hamburg-altona");
		$city->setTitle("Critical Mass Hamburg-Altona");
		$city->setUrl("http://www.critical-mass-altona.de/");
		$city->setFacebook("https://www.facebook.com/CriticalMassAltona");
		$city->setTwitter("https://www.twitter.com/cm_altona‎");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-altona", $city);

		// Critical Mass Hannover
		$city = new City();
		$city->setCity("hannover");
		$city->setTitle("Critical Mass Hannover");
		$city->setUrl("http://criticalmasshannover.wordpress.com/");
		$city->setFacebook("https://www.facebook.com/pages/Critical-Mass-Hannover/483028381718877");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-hannover", $city);

		// Critical Mass Kassel
		$city = new City();
		$city->setCity("kassel");
		$city->setTitle("Critical Mass Kassel");
		$city->setUrl("http://www.myspace.com/criticalmasskassel");
		$city->setFacebook("");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-kassel", $city);

		// Critical Mass Kiel
		$city = new City();
		$city->setCity("kiel");
		$city->setTitle("Critical Mass Kiel");
		$city->setUrl("http://criticalmasskiel.wordpress.com/");
		$city->setFacebook("https://www.facebook.com/CriticalMassKiel");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-kiel", $city);

		// Critical Mass Koblenz
		$city = new City();
		$city->setCity("koblenz");
		$city->setTitle("Critical Mass Koblenz");
		$city->setUrl("http://www.criticalmasskoblenz.blogspot.de/");
		$city->setFacebook("https://www.facebook.com/criticalmass.koblenz");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-koblenz", $city);

		// Critical Mass Koeln
		$city = new City();
		$city->setCity("koeln");
		$city->setTitle("Critical Mass Koeln");
		$city->setUrl("http://www.critical-mass-cologne.de/");
		$city->setFacebook("https://www.facebook.com/critical.mass.koeln");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-koeln", $city);

		// Critical Mass Leipzig
		$city = new City();
		$city->setCity("leipzig");
		$city->setTitle("Critical Mass Leipzig");
		$city->setUrl("http://criticalmass.wikia.com/wiki/Leipzig");
		$city->setFacebook("");
		$city->setTwitter("https://twitter.com/cmleipzig");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-leipzig", $city);

		// Critical Mass Leipzig
		$city = new City();
		$city->setCity("luebeck");
		$city->setTitle("Critical Mass Lübeck");
		$city->setUrl("");
		$city->setFacebook("https://www.facebook.com/CriticalMassLubeck");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-luebeck", $city);

		// Critical Mass Magdeburg
		$city = new City();
		$city->setCity("magdeburg");
		$city->setTitle("Critical Mass Magdeburg");
		$city->setUrl("");
		$city->setFacebook("https://www.facebook.com/criticalmass.magdeburg");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-magdeburg", $city);

		// Critical Mass Mannheim
		$city = new City();
		$city->setCity("mannheim");
		$city->setTitle("Critical Mass Mannheim");
		$city->setUrl("http://criticalmassmannheim.blogspot.de/");
		$city->setFacebook("https://www.facebook.com/pages/Critical-mass-bike-flash-mob-Mannheim/165766566835816");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-magdeburg", $city);

		// Critical Mass Nürnberg
		$city = new City();
		$city->setCity("nuernberg");
		$city->setTitle("Critical Mass Nürnberg");
		$city->setUrl("http://www.myspace.com/critical_mass_nuernberg");
		$city->setFacebook("");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-nuernberg", $city);

		// Critical Mass Oldenburg
		$city = new City();
		$city->setCity("oldenburg");
		$city->setTitle("Critical Mass Oldenburg");
		$city->setUrl("http://criticalmassoldenburg.blogsport.de/");
		$city->setFacebook("https://www.facebook.com/CriticalMassOldenburg");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-oldenburg", $city);

		// Critical Mass Rostock
		$city = new City();
		$city->setCity("rostock");
		$city->setTitle("Critical Mass Rostock");
		$city->setUrl("http://www.cmrostock.de/");
		$city->setFacebook("https://www.facebook.com/CriticalMassRostock");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-rostock", $city);

		// Critical Mass Stuttgart
		$city = new City();
		$city->setCity("stuttgart");
		$city->setTitle("Critical Mass Stuttgart");
		$city->setUrl("http://criticalmassstuttgart.wordpress.com/");
		$city->setFacebook("https://www.facebook.com/getonyourbike");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-stuttgart", $city);

		// Critical Mass Trier
		$city = new City();
		$city->setCity("trier");
		$city->setTitle("Critical Mass Trier");
		$city->setUrl("");
		$city->setFacebook("");
		$city->setTwitter("https://twitter.com/VelomobTrier");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-trier", $city);

		// Critical Mass Wuppertal
		$city = new City();
		$city->setCity("Wuppertal");
		$city->setTitle("Critical Mass Wuppertal");
		$city->setUrl("http://cmwpt.wordpress.com/");
		$city->setFacebook("https://www.facebook.com/CriticalMassWuppertal");
		$city->setTwitter("");

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