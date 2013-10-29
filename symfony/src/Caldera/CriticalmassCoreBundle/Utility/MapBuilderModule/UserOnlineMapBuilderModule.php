<?php
/**
 * Created by PhpStorm.
 * User: Malte
 * Date: 29.10.13
 * Time: 20:49
 */

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilderModule;

class UserOnlineMapBuilderModule extends BaseMapBuilderModule
{
    public function execute()
    {
        $users = array();

        foreach ($this->mapBuilder->positionArray->getPositions() as $position)
        {
            if (!in_array($position->getUser(), $users))
            {
                $users[] = $position->getUser();
            }
        }

        $this->mapBuilder->response['userOnline'] = count($users);
    }
} 