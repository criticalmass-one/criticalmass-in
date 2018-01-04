<?php

namespace CCriticalmass\Component\Image\ExifReader;

use Criticalmass\Bundle\AppBundle\Entity\Photo;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * @deprecated
 */
abstract class AbstractExifReader
{
    /**
     * @var Photo $photo
     */
    protected $photo;

    protected $exif;

    protected $filename;

    public function __construct(UploaderHelper $uploaderHelper, $rootDirectory)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->rootDirectory = $rootDirectory . '/../web';
    }

    public function setPhoto(Photo $photo)
    {
        $this->photo = $photo;

        $this->filename = $this->rootDirectory . $this->uploaderHelper->asset($this->photo, 'imageFile');

        $this->exif = exif_read_data($this->filename, 0, true);

        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }
}
