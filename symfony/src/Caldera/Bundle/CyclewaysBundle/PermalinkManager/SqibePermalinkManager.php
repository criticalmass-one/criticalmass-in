<?php

namespace Caldera\Bundle\CyclewaysBundle\Permalinkmanager;

use Caldera\Bundle\CalderaBundle\Entity\Incident;
use Curl\Curl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SqibePermalinkManager
{
    public function __construct()
    {

    }

    protected function createPermalink(Incident $incident)
    {
        $url = $this->generateUrl(
            'caldera_cycleways_incident_show',
            [
                'slug' => $incident->getSlug()
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $apiUrl =  $this->getParameter('sqibe.api_url');

        $data = [
            'url' => $url,
            'title' => $incident->getTitle(),
            'format'   => 'json',
            'action'   => 'shorturl',
            'username' => $this->getParameter('sqibe.api_username'),
            'password' => $this->getParameter('sqibe.api_password')
        ];

        $curl = new Curl();
        $curl->post(
            $apiUrl,
            $data
        );

        $permalink = $curl->response->shorturl;

        $incident->setPermalink($permalink);
    }
}