<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stellt eine einzelne Tour einer Critical Mass dar.
 *
 * @ORM\Entity(repositoryClass="Caldera\CriticalmassBundle\Entity\RideRepository")
 * @ORM\Table(name="ride")
 */
class Ride
{
	/**
	 * Numerische ID der Tour.
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * Numerische ID der dazugehörigen Stadt, in der die Tour stattfindet.
	 *
	 * @ORM\ManyToOne(targetEntity="City", inversedBy="rides")
	 * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
	 */
	protected $city;

	/**
	 * Datum der Tour vom Typ DateTime.
	 *
	 * @ORM\Column(type="date")
	 */
	protected $date;

	/**
	 * Uhrzeit der Tour vom Typ DateTime.
	 *
	 * @ORM\Column(type="time")
	 */
	protected $time;

	/**
	 * Bezeichnung des Treffpunktes der Tour als Zeichenkette.
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	protected $location;

	/**
	 * Zeichenkette eines Karten-Embeddings, beispielsweise von Google-Maps. Wird
	 * anschließend unter dem Treffpunkt eingebunden.
	 *
	 * @ORM\Column(type="text")
	 */
	protected $map;

	/**
	 * Get id
	 *
	 * @return integer 
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set date
	 *
	 * @param \DateTime $date
	 * @return Ride
	 */
	public function setDate($date)
	{
		$this->date = $date;

		return $this;
	}

	/**
	 * Get date
	 *
	 * @return \DateTime 
	 */
	public function getDate()
	{
		return $this->date;
	}

	/**
	 * Set time
	 *
	 * @param \DateTime $time
	 * @return Ride
	 */
	public function setTime($time)
	{
		$this->time = $time;

		return $this;
	}

	/**
	 * Get time
	 *
	 * @return \DateTime 
	 */
	public function getTime()
	{
		return $this->time;
	}

	/**
	 * Set location
	 *
	 * @param string $location
	 * @return Ride
	 */
	public function setLocation($location)
	{
		$this->location = $location;

		return $this;
	}

/**
 * Get location
 *
 * @return string 
 */
	public function getLocation()
	{
		return $this->location;
	}

	/**
	 * Set city_id
	 *
	 * @param \Caldera\CriticalmassBundle\Entity\City $cityId
	 * @return Ride
	 */
	public function setCity(\Caldera\CriticalmassBundle\Entity\City $city = null)
	{
		$this->city = $city;

		return $this;
	}

	/**
	 * Get city_id
	 *
	 * @return \Caldera\CriticalmassBundle\Entity\City 
	 */
	public function getCity()
	{
		return $this->city;
	}

	/**
	 * Set map
	 *
	 * @param string $map
	 * @return Ride
	 */
	public function setMap($map)
	{
		$this->map = $map;

		return $this;
	}

	/**
	 * Get map
	 *
	 * @return string 
	 */
	public function getMap()
	{
		return $this->map;
	}
}