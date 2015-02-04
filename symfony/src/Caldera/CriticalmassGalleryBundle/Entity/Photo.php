<?php

namespace Caldera\CriticalmassGalleryBundle\Entity;

use Caldera\CriticalmassGalleryBundle\Utility\PhotoUtility;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Photo
 *
 * @ORM\Table(name="Photo")
 * @ORM\Entity
 */
class Photo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="photos")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Caldera\CriticalmassCoreBundle\Entity\Ride", inversedBy="photos")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\ManyToOne(targetEntity="Caldera\CriticalmassCoreBundle\Entity\City", inversedBy="photos")
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
     * @ORM\Column(type="datetime")
     */
    protected $dateTime;

    public function __construct()
    {
        $this->dateTime = new \DateTime();
    }

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
        $this->dateTime = new \DateTime();

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
            $this->getId() . "." . strtolower($this->getFile()->getClientOriginalExtension())
        );

        // set the path property to the filename where you've saved the file
        $this->filePath = $this->getUploadRootDir() . $this->getId() . "." . strtolower($this->getFile()->getClientOriginalExtension());

        $utility = new PhotoUtility();

        if ($this->getFile()->getClientOriginalExtension() == "jpg" ||
            $this->getFile()->getClientOriginalExtension() == "JPG" ) {

            $smallSize = $utility->reduceSize($this, 0.5);
            $utility->makeSmallPhotoJPG($this, $smallSize['width'], $smallSize['height'], "_klein");
            $utility->makeSmallPhotoJPG($this, 200, 200, "_thumbnail");
            $utility->getMetaInfos($this);

        }

        if ($this->getFile()->getClientOriginalExtension() == "png" ||
            $this->getFile()->getClientOriginalExtension() == "PNG" ) {

            $smallSize = $utility->reduceSize($this, 0.5);
            $utility->makeSmallPhotoPNG($this, $smallSize['width'], $smallSize['height'], "_klein");
            $utility->makeSmallPhotoPNG($this, 200, 200, "_thumbnail");

        }
    }

    /**
     * @return mixed
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getFilePathByDepth($depth) {
        $result = "";

        for ($i = 0; $i < $depth; $i++) {
            $result = $result . "../";
        }

        $result = $result . $this->filePath;

        return $result;
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

    /**
     * @return mixed
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param mixed $dateTime
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     * @return mixed
     */
    public function getSmallFile()
    {
        return $this->small_file;
    }

    /**
     * @param mixed $small_file
     */
    public function setSmallFile($small_file)
    {
        $this->small_file = $small_file;
    }

    public function toSmallPath($path) {
        return str_replace("" . $this->id, $this->id . "_klein", $path);
    }

    public function toThumbnailPath($path) {
        return str_replace("" . $this->id, $this->id . "_thumbnail", $path);
    }
}
