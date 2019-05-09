<?php declare(strict_types=1);

namespace App\Criticalmass\Image\GoogleCloud\ExportDataHandler;

use App\Entity\Photo;

interface ExportDataHandlerInterface
{
    public function calculateForPhoto(Photo $photo): Photo;
}