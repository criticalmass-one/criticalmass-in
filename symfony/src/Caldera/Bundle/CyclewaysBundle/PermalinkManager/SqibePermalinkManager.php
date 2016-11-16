<?php

namespace Caldera\Bundle\CyclewaysBundle\PermalinkManager;

use Caldera\Bundle\CalderaBundle\Entity\Incident;
use Curl\Curl;
use stdClass;
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
            'action'   => 'shorturl'
        ];

        $response = $this->postCurl($data);

        if (!isset($response->shurturl)) {
            return '';
        }

        $permalink = $response->shorturl;

        $incident->setPermalink($permalink);

        return $permalink;
    }

    public function getUrl(Incident $incident): string
    {
        $data = [
            'shorturl' => $this->getKeyword($incident),
            'format'   => 'json',
            'action'   => 'expand'
        ];

        $response = $this->postCurl($data);

        if (isset($response->errorCode) && $response->errorCode == 404) {
            return '';
        }

        $longUrl = $response->longurl;

        return $longUrl;
    }

    public function updatePermalink(Incident $incident): bool
    {
        $url = $this->generateUrl($incident);

        $data = [
            'url' => $url,
            'shorturl' => $this->getKeyword($incident),
            'format'   => 'json',
            'action'   => 'update'
        ];

        $response = $this->postCurl($data);

        if (isset($response->statusCode) && $response->statusCode == 200) {
            return true;
        }

        return false;
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

        $url = str_replace('http://', 'https://', $url);

        return $url;
    }

    protected function postCurl(array $data): stdClass
    {
        $loginArray = [
            'username' => $this->apiUsername,
            'password' => $this->apiPassword
        ];

        $data = array_merge($data, $loginArray);

        $curl = new Curl();
        $curl->post(
            $this->apiUrl,
            $data
        );

        return $curl->response;
    }
}