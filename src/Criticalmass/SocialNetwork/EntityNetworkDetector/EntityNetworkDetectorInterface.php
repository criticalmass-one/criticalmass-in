<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\EntityNetworkDetector;

use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Entity\SocialNetworkProfile;

interface EntityNetworkDetectorInterface
{
    public function detect(SocialNetworkProfile $socialNetworkProfile): ?NetworkInterface;
}
