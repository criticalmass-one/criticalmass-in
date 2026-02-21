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
        $scheme = parse_url($path, PHP_URL_SCHEME);

        if (!in_array($scheme, ['http', 'https'], true)) {
            throw new \InvalidArgumentException(sprintf('Only http and https URLs are allowed, got "%s".', $scheme));
        }

        $host = parse_url($path, PHP_URL_HOST);

        if ($host && (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false && !preg_match('/\.[a-z]{2,}$/i', $host))) {
            throw new \InvalidArgumentException('Requests to private or reserved IP ranges are not allowed.');
        }

        return $this->imagine->load(file_get_contents($path));
    }
}
