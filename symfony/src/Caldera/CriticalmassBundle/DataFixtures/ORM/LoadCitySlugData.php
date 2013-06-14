<?php

namespace Caldera\CriticalmassBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Caldera\CriticalmassBundle\Entity\City;
use Caldera\CriticalmassBundle\Entity\CitySlug;

class LoadCitySlugData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-augsburg"));
		$citySlug->setSlug("augsburg");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-berlin"));
		$citySlug->setSlug("berlin");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-bochum"));
		$citySlug->setSlug("bochum");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-braunschweig"));
		$citySlug->setSlug("braunschweig");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-bremen"));
		$citySlug->setSlug("bremen");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-dortmund"));
		$citySlug->setSlug("dortmund");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-dresden"));
		$citySlug->setSlug("dresden");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-duisburg"));
		$citySlug->setSlug("duisburg");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-duesseldorf"));
		$citySlug->setSlug("duesseldorf");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-duesseldorf"));
		$citySlug->setSlug("dusseldorf");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-essen"));
		$citySlug->setSlug("essen");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-flensburg"));
		$citySlug->setSlug("flensburg");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-frankfurt"));
		$citySlug->setSlug("frankfurt");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-freiburg"));
		$citySlug->setSlug("freiburg");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-hamburg"));
		$citySlug->setSlug("hamburg");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-altona"));
		$citySlug->setSlug("altona");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-altona"));
		$citySlug->setSlug("hamburg-altona");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-altona"));
		$citySlug->setSlug("hamburgaltona");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-hannover"));
		$citySlug->setSlug("hannover");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-kassel"));
		$citySlug->setSlug("kassel");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-kiel"));
		$citySlug->setSlug("kiel");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-koblenz"));
		$citySlug->setSlug("koblenz");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-koeln"));
		$citySlug->setSlug("koeln");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-koeln"));
		$citySlug->setSlug("koln");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-koeln"));
		$citySlug->setSlug("cologne");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-leipzig"));
		$citySlug->setSlug("leipzig");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-luebeck"));
		$citySlug->setSlug("luebeck");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-luebeck"));
		$citySlug->setSlug("lubeck");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-magdeburg"));
		$citySlug->setSlug("magdeburg");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-mannheim"));
		$citySlug->setSlug("mannheim");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-nuernberg"));
		$citySlug->setSlug("nurnberg");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-oldenburg"));
		$citySlug->setSlug("oldenburg");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-rostock"));
		$citySlug->setSlug("rostock");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-stuttgart"));
		$citySlug->setSlug("stuttgart");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-trier"));
		$citySlug->setSlug("trier");

		$manager->persist($citySlug);
		$manager->flush();

		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-wuppertal"));
		$citySlug->setSlug("wuppertal");

		$manager->persist($citySlug);
		$manager->flush();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return 5;
	}
}
