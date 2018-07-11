<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Sharing\Network;

use AppBundle\Criticalmass\Sharing\ShareableInterface\Shareable;

interface ShareNetworkInterface
{
    public function getIdentifier(): string;
    public function createUrlForShareable(Shareable $shareable): string;
    public function getName(): string;
    public function getIcon(): string;
    public function getBackgroundColor(): string;
    public function getTextColor(): string;
    public function openShareWindow(): bool;
}
