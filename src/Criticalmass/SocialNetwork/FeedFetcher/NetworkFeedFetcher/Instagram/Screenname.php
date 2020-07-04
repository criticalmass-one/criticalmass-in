<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Instagram;

use App\Entity\SocialNetworkProfile;

class Screenname
{
    private function __construct()
    {

    }

    public static function extractScreenname(SocialNetworkProfile $socialNetworkProfile): ?string
    {
        $identifierParts = explode('/', $socialNetworkProfile->getIdentifier());

        do {
            $screenname = array_pop($identifierParts);
        } while (!$screenname && '/' !== $screenname);

        return $screenname;
    }

    public static function isValidScreenname(string $screenname): bool
    {
        return (bool)preg_match('/^[a-zA-Z0-9._]+$/', $screenname);
    }
}