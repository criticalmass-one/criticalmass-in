<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\EntityNetworkDetector;

use App\Entity\SocialNetworkProfile;
use Caldera\SocialNetworkBundle\Network\NetworkInterface;

interface EntityNetworkDetectorInterface
{
    public function detect(SocialNetworkProfile $socialNetworkProfile): ?NetworkInterface;
}
