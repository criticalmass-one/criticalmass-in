<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Caldera\CriticalmassBundle\Entity\UserRepository")
 */
class User implements UserInterface, \Serializable
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=25, unique=true)
	 */
	private $username;

	/**
	 * @ORM\Column(type="string", length=32)
	 */
	private $salt;

	/**
	 * @ORM\Column(type="string", length=40)
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=60, unique=true)
	 */
	private $email;

	/**
	 * @ORM\Column(name="is_active", type="boolean")
	 */
	private $isActive;

	public function __construct()
	{
		$this->isActive = true;
		$this->salt = md5(uniqid(null, true));
	}

	/**
	 * @inheritDoc
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @inheritDoc
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * @inheritDoc
	 */
	public function getSalt()
	{
		return $this->salt;
	}

	/**
	 * @inheritDoc
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @inheritDoc
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @inheritDoc
	 */
	public function setEmail($email)
	{
		$this->email = $email;
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

	/**
	 * @inheritDoc
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * @inheritDoc
	 */
	public function getRoles()
	{
		return array('ROLE_USER');
	}

	/**
	 * @inheritDoc
	 */
	public function eraseCredentials()
	{
	}

	/**
	 * @see \Serializable::serialize()
	 */
	public function serialize()
	{
		return serialize(array(
			$this->id,
		));
	}

	/**
	 * @see \Serializable::unserialize()
	 */
	public function unserialize($serialized)
	{
		list (
			$this->id,
		) = unserialize($serialized);
	}
}