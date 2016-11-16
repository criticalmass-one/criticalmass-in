<?php

namespace Caldera\Bundle\CyclewaysBundle\PermalinkManager;

use Caldera\Bundle\CalderaBundle\Entity\Incident;
use Curl\Curl;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class SqibePermalinkManager
{
    /** @var Router $router */
    private $router;

    /** @var string $apiUrl */
    private $apiUrl;

    /** @var string $apiUsername */
    private $apiUsername;

    /** @var string $apiPassword */
    private $apiPassword;

    public function __construct(Router $router, string $apiUrl, string $apiUsername, string $apiPassword)
    {
        $this->router = $router;

        $this->apiUrl = $apiUrl;
        $this->apiUsername = $apiUsername;
        $this->apiPassword = $apiPassword;
    }

    public function createPermalink(Incident $incident): string
    {
        $data = [
            'url' => $this->generateUrl($incident),
            'title' => $incident->getTitle(),
            'format'   => 'json',
            'action'   => 'shorturl',
            'username' => $this->apiUsername,
            'password' => $this->apiPassword
        ];

        $curl = new Curl();
        $curl->post(
            $this->apiUrl,
            $data
        );

        $permalink = $curl->response->shorturl;

        $incident->setPermalink($permalink);

        return $permalink;
    }

    public function getUrl(Incident $incident): string
    {
        $permalinkParts = explode('/', $incident->getPermalink());
        $shortUrl = array_pop($permalinkParts);

        $data = [
            'shorturl' => $this->getKeyword($incident),
            'format'   => 'json',
            'action'   => 'expand',
            'username' => $this->apiUsername,
            'password' => $this->apiPassword
        ];

        $curl = new Curl();
        $curl->post(
            $this->apiUrl,
            $data
        );

        $response = $curl->response;

        if (isset($response->errorCode) && $response->errorCode == 404) {
            return '';
        }

        $longUrl = $curl->response->longurl;

        return $longUrl;
    }

    protected function getKeyword(Incident $incident): string
    {
        $permalinkParts = explode('/', $incident->getPermalink());
        $keyword = array_pop($permalinkParts);

        return $keyword;
    }

    protected function generateUrl(Incident $incident): string
    {
        $url = $this->router->generate(
            'caldera_cycleways_incident_show',
            [
                'slug' => $incident->getSlug()
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $url;
    }
}