<?php declare(strict_types=1);

namespace App\Criticalmass\SeoPage;

use App\EntityInterface\PhotoInterface;
use App\EntityInterface\RouteableInterface;

interface SeoPageInterface
{
    public function setTitle(string $title): SeoPageInterface;
    public function setDescription(string $description): SeoPageInterface;
    public function setPreviewPhoto(PhotoInterface $object): SeoPageInterface;
    public function setCanonicalLink(string $link): SeoPageInterface;
    public function setCanonicalForObject(RouteableInterface $object): SeoPageInterface;
}
