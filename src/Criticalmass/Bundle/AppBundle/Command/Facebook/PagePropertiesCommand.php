<?php

namespace Criticalmass\Bundle\AppBundle\Command\Facebook;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Symfony\Component\Console\Command\Command;

class PagePropertiesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('criticalmass:facebook:pageproperties')
            ->setDescription('');
    }

    protected function getPageId(City $city): ?string
    {
        $facebook = $city->getFacebook();

        if (strpos($facebook, 'https://www.facebook.com/') == 0) {
            $facebook = rtrim($facebook, "/");

            $parts = explode('/', $facebook);

            $pageId = array_pop($parts);

            return $pageId;
        }

        return null;
    }

}
