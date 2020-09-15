<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\NetworkDetector;

use App\Criticalmass\SocialNetwork\Network\NetworkInterface;

interface NetworkDetectorInterface
{
    public function detect(string $url): ?NetworkInterface;

}
