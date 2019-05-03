<?php declare(strict_types=1);

namespace App\Criticalmass\Image\ExifHandler;

abstract class AbstractExifHandler implements ExifHandlerInterface
{
    /** @var string $uploadDestinationPhoto */
    protected $uploadDestinationPhoto;

    public function __construct(string $uploadDestinationPhoto)
    {
        $this->uploadDestinationPhoto = $uploadDestinationPhoto;
    }
}
