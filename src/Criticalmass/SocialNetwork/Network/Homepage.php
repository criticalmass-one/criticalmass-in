<?php

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

class Homepage extends AbstractNetwork
{
    protected $name = 'Homepage';

    protected $icon = 'fa-globe';

    protected $backgroundColor = 'rgb(85, 172, 238)';

    protected $textColor = 'white';

    protected $detectorPriority = -100;

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        return filter_var($socialNetworkProfile->getIdentifier(), FILTER_VALIDATE_URL);
    }
}
