<?php declare(strict_types=1);

namespace App\Criticalmass\UploadFaker;

use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractUploadFaker implements UploadFakerInterface
{
    final const TMP = '/tmp';

    /** @var Filesystem $filesystem */
    protected $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }
}