<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\NetworkDetector;

use App\Entity\SocialNetworkProfile;
use App\Criticalmass\SocialNetwork\Network\NetworkInterface;

interface NetworkDetectorInterface
{
    public function detect(SocialNetworkProfile $socialNetworkProfile): ?NetworkInterface;

}
