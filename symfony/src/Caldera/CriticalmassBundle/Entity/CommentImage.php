<?php

namespace Caldera\CriticalmassBundle\Entity;

use Caldera\CriticalmassBundle\Utility as Utility;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Doctrine\ORM\Mapping as ORM;

/**
 * Diese Klasse repraesentiert ein Bild, das zu einem Kommentar hochgeladen
 * wurde.
 *
 * @ORM\Entity()
 * @ORM\Table(name="commentimage")
 * @ORM\HasLifecycleCallbacks
 */
class CommentImage
{
	/**
	 * Numerische ID dieses Bildes.
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * Autor dieses Bildes.
	 *
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * Zeitpunkt der Erstellung dieser Entitaet.
	 *
	 * @ORM\Column(type="datetime")
	 */
	protected $creationDateTime;

	/**
	 * Name des Bildes.
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	protected $name = "Image";

	/**
	 * Speicherort der Bilddatei.
	 *
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	protected $path;

	/**
	 * Breite des verkleinerten Vorschaubildes.
	 *
	 * @ORM\Column(type="smallint")
	 */
	protected $resizedWidth = 0;

	/**
	 * Hoehe des verkleinerten Vorschaubildes.
	 *
	 * @ORM\Column(type="smallint")
	 */
	protected $resizedHeight = 0;

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
     * @return CommentImage
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
     * Set user
     *
     * @param \Caldera\CriticalmassBundle\Entity\User $user
     * @return CommentImage
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
     * Set name
     *
     * @param string $name
     * @return CommentImage
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return CommentImage
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

		public function getFile()
		{
			return $this->file;
		}

		public function getWebPath()
		{
			return 'http://localhost:8000/uploads/commentimages/'.$this->getId().'.jpeg';
		}

	public function getResizedWebPath()
	{
		return $this->getWebPath();
		/*
		$ir = new Utility\ImageResizer($this);
		$ir->resizeLongSideToLength(300);

		return $ir->getResizedPath();*/
	}

    /**
     * Set resizedWidth
     *
     * @param integer $resizedWidth
     * @return CommentImage
     */
    public function setResizedWidth($resizedWidth)
    {
        $this->resizedWidth = $resizedWidth;
    
        return $this;
    }

    /**
     * Get resizedWidth
     *
     * @return integer 
     */
    public function getResizedWidth()
    {
        return $this->resizedWidth;
    }

    /**
     * Set resizedHeight
     *
     * @param integer $resizedHeight
     * @return CommentImage
     */
    public function setResizedHeight($resizedHeight)
    {
        $this->resizedHeight = $resizedHeight;
    
        return $this;
    }

    /**
     * Get resizedHeight
     *
     * @return integer 
     */
    public function getResizedHeight()
    {
        return $this->resizedHeight;
    }
}