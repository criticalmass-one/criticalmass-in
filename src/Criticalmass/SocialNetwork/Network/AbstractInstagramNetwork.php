<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

abstract class AbstractInstagramNetwork extends AbstractNetwork
{
    protected $name = 'Instagram';

    protected $icon = 'fa-instagram';

    protected $backgroundColor = 'rgb(85, 172, 238)';

    protected $textColor = 'white';
}
