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
        $url = $this->router->generate(
            'caldera_cycleways_incident_show',
            [
                'slug' => $incident->getSlug()
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $data = [
            'url' => $url,
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
    }

    public function getUrl(Incident $incident): string
    {
        $permalinkParts = explode('/', $incident->getPermalink());
        $shortUrl = array_pop($permalinkParts);

        $data = [
            'shorturl' => $shortUrl,
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
}