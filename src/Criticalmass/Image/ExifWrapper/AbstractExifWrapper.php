<?php declare(strict_types=1);

namespace App\Criticalmass\Image\ExifWrapper;

use League\Flysystem\FilesystemInterface as FlysystemFilesystemInterface;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

abstract class AbstractExifWrapper implements ExifWrapperInterface
{
    /** @var SymfonyFilesystem $symfonyFilesystem */
    protected $symfonyFilesystem;

    public function __construct(protected FlysystemFilesystemInterface $flysystemFilesystem)
    {
        $this->symfonyFilesystem = new SymfonyFilesystem();
    }
}