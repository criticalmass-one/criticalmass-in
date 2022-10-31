<?php declare(strict_types=1);

namespace App\Criticalmass\Image\ExifHandler;

use App\Criticalmass\Image\ExifWrapper\ExifWrapperInterface;

abstract class AbstractExifHandler implements ExifHandlerInterface
{
    public function __construct(protected ExifWrapperInterface $exifWrapper)
    {
    }
}
