<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

abstract class AbstractYoutubeNetwork extends AbstractNetwork
{
    protected $name = 'YouTube';

    protected $icon = 'fa-youtube';

    protected $backgroundColor = 'rgb(220, 78, 65)';

    protected $textColor = 'white';
}
