<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

abstract class AbstractInstagramNetwork extends AbstractNetwork
{
    protected string $icon = 'fab fa-instagram';

    protected string $backgroundColor = 'rgb(203, 44, 128)';

    protected string $textColor = 'white';
}
