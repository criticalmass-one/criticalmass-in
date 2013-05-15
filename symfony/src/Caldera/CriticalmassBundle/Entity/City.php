<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="city")
 */
class City
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
  protected $id;

	/**
	 * @ORM\Column(type="string", length=50)
	 */
	protected $name;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	protected $title;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	protected $url;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	protected $facebook;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	protected $twitter;
}