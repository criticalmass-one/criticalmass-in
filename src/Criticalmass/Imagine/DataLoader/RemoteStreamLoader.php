<?php declare(strict_types=1);

namespace App\Criticalmass\Imagine\DataLoader;

use Liip\ImagineBundle\Binary\Loader\LoaderInterface;
use Imagine\Image\ImagineInterface;

class RemoteStreamLoader implements LoaderInterface
{
    /** @var ImagineInterface $imagine */
    protected $imagine;

    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }

    public function find($path)
    {
        return $this->imagine->load(file_get_contents($path));
    }
}
