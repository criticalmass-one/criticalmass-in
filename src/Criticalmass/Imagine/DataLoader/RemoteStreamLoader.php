<?php declare(strict_types=1);

namespace App\Criticalmass\Imagine\DataLoader;

use Liip\ImagineBundle\Binary\Loader\LoaderInterface;
use Imagine\Image\ImagineInterface;

class RemoteStreamLoader implements LoaderInterface
{
    public function __construct(protected ImagineInterface $imagine)
    {
    }

    public function find($path)
    {
        return $this->imagine->load(file_get_contents($path));
    }
}
