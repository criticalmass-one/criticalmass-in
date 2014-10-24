<?php

namespace Caldera\CriticalmassGalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Photos
 *
 * @ORM\Table(name="Photos")
 * @ORM\Entity
 */
class Photos
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Caldera\CriticalmassCoreBundle\Entity\Ride", inversedBy="posts")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\ManyToOne(targetEntity="Caldera\CriticalmassCoreBundle\Entity\City", inversedBy="posts")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $longitude;

    /**
     * @Assert\File(maxSize="6000000")
     */
    protected $file;

    /**
     * @Assert\File(maxSize="6000000")
     */
    protected $small_file;

    /**
     * @ORM\Column(type="text")
     */
    protected $filePath;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $licence = true;

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
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getRide()
    {
        return $this->ride;
    }

    /**
     * @param mixed $ride
     */
    public function setRide($ride)
    {
        $this->ride = $ride;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $path
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * @param mixed $licence
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;
    }

    public function handleUpload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getId() . "." . $this->getFile()->getClientOriginalExtension()
        );

        error_log("pre_output");

        // set the path property to the filename where you've saved the file
        $this->filePath = "/../../".$this->getUploadRootDir() . $this->getId() . "." . $this->getFile()->getClientOriginalExtension();

        // Content type
        header('Content-Type: image/jpeg');

        // Get new dimensions
        list($width, $height) = getimagesize($this->getFile()->getContent());
        $new_width = 50;
        $new_height = 50;

        // Resample
        $this->small_file = imagecreatetruecolor($new_width, $new_height);
        $image = imagecreatefromstring($this->getFile()->getContent());
        imagecopyresampled($this->small_file, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        error_log("output");

        // Output
        //imagejpeg($image_p, null, 100);
        $this->small_file->move($this->getUploadRootDir(),
            $this->getId() . "_klein." . $this->getFile()->getClientOriginalExtension());
    }

    /**
     * @return mixed
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getUploadRootDir() {
        return "photos/";
    }

    /**
     * @param mixed $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

}
