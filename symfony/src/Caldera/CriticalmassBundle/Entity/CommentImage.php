<?php

namespace Caldera\CriticalmassBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="commentimage")
 * @ORM\HasLifecycleCallbacks
 */
class CommentImage
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $creationDateTime;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	protected $name = "Foo";

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	protected $path;

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

    private $temp;
		private $file;

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

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {//$this->getFile()->guessExtension();
			$newPath = '/var/www/criticalmass.in/symfony/web/uploads/commentimages/';
			$newFilename = 'abc'.'.'.$this->getFile()->guessExtension();

			$this->getFile()->move($newPath, $newFilename);

			$this->setPath($newPath.$newFilename);
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {

    }

    /**
     * @ORM\PostRemove()
     */
/*    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }*/
	public function getWebPath()
	{
		return 'http://www.criticalmass.in/uploads/commentimages/abc.jpeg';
	}
}
