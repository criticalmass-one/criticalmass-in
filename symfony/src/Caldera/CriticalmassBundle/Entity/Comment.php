<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Die Comment-Entitaet repraesentiert einen Kommentar, der von angemeldeten
 * Benutzern zu einer Tour abgegeben werden kann.
 *
 * @ORM\Entity()
 * @ORM\Table(name="comment")
 */
class Comment
{
	/**
	 * ID des Kommentars.
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * Autor des Kommentars.
	 *
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * Tour des Kommentars.
	 *
	 * @ORM\ManyToOne(targetEntity="Ride", inversedBy="comments")
	 * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
	 */
	protected $ride;

	/**
	 * Zeitpunkt der Erstellung.
	 *
	 * @ORM\Column(type="datetime")
	 */
	protected $creationDateTime;

	/**
	 * Eigentlicher Text des Kommentars.
	 *
	 * @ORM\Column(type="text")
	 */
	protected $text;

	/**
	 * Optional hochgeladenes Foto des Kommentars.
	 *
	 * @ORM\OneToOne(targetEntity="CommentImage")
	 * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
	 */
	private $image;

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
     * Set creationDateTime
     *
     * @param \DateTime $creationDateTime
     * @return Comment
     */
    public function setCreationDateTime($creationDateTime)
    {
        $this->creationDateTime = $creationDateTime;

        return $this;
    }

    /**
     * Get creationDateTime
     *
     * @return \DateTime 
     */
    public function getCreationDateTime()
    {
        return $this->creationDateTime;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Comment
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set user
     *
     * @param \Caldera\CriticalmassBundle\Entity\User $user
     * @return Comment
     */
    public function setUser(\Caldera\CriticalmassBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Caldera\CriticalmassBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set ride
     *
     * @param \Caldera\CriticalmassBundle\Entity\Ride $ride
     * @return Comment
     */
    public function setRide(\Caldera\CriticalmassBundle\Entity\Ride $ride = null)
    {
        $this->ride = $ride;

        return $this;
    }

    /**
     * Get ride
     *
     * @return \Caldera\CriticalmassBundle\Entity\Ride 
     */
    public function getRide()
    {
        return $this->ride;
    }

    public function setImage(\Symfony\Component\HttpFoundation\File\UploadedFile $image = null)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Set image
     *
     * @param \Caldera\CriticalmassBundle\Entity\CommentImage $image
     * @return Comment
     */
    public function setImage2(\Caldera\CriticalmassBundle\Entity\CommentImage $image = null)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return \Caldera\CriticalmassBundle\Entity\CommentImage 
     */
    public function getImage()
    {
        return $this->image;
    }
}