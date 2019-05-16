<?php declare(strict_types=1);

namespace App\Criticalmass\Image\ExifHandler;

use App\Criticalmass\Image\ExifWrapper\ExifWrapperInterface;

abstract class AbstractExifHandler implements ExifHandlerInterface
{
    /** @var ExifWrapperInterface $exifWrapper */
    protected $exifWrapper;

    public function __construct(ExifWrapperInterface $exifWrapper)
    {
        $this->exifWrapper = $exifWrapper;
    }
}
