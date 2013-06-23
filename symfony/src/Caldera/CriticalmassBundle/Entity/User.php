<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Caldera\CriticalmassBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Hasht die E-Mail-Adresse per MD5, um das dazugehörige Gravartar-Profilbild
	 * aufrufen zu können.
	 *
	 * @return String MD5-gehashte E-Mail-Adresse
	 */
	public function getGravatarHash()
	{
		return md5($this->getEmail());
	}

}