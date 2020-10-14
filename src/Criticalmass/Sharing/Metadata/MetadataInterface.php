<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing\Metadata;

use App\Criticalmass\Sharing\ShareableInterface\Shareable;

interface MetadataInterface
{
    public function getShareUrl(Shareable $shareable): string;
    public function getShareTitle(Shareable $shareable): ?string;
    public function getShareIntro(Shareable $shareable): ?string;
    public function getShorturl(Shareable $shareable): ?string;
    public function setShorturl(Shareable $shareable, string $shorturl): Shareable;
}
