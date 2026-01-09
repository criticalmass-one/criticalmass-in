<?php declare(strict_types=1);

namespace App\Criticalmass\Website\Parser;

use App\Entity\CrawledWebsite;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use \simplehtmldom_1_5\simple_html_dom as HtmlDomElement;
use Sunra\PhpSimple\HtmlDomParser;

class Parser implements ParserInterface
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {

    }

    public function parse(string $url): ?CrawledWebsite
    {
        if ($html = $this->fetchHtml($url)) {
            return $this->parseHtml($html, $url);
        }

        return null;
    }

    protected function fetchHtml(string $url): ?HtmlDomElement
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
                'timeout' => 10,
            ]);

            if (200 === $response->getStatusCode()) {
                $htmlString = $response->getContent(false);

                $htmlDomElement = HtmlDomParser::str_get_html($htmlString);

                if ($htmlDomElement === false) {
                    return null;
                }

                return $htmlDomElement;
            }
        } catch (TransportExceptionInterface |
        ClientExceptionInterface |
        RedirectionExceptionInterface |
        ServerExceptionInterface |
        \Exception $e) {
            return null;
        }

        return null;
    }

    protected function parseHtml(HtmlDomElement $element, string $url): CrawledWebsite
    {
        $cw = new CrawledWebsite();
        $cw->setUrl($url);

        $this->findFirstMatchingElement($element, $cw, 'title', 'title', 'innertext');
        $this->findFirstMatchingElement($element, $cw, 'description', 'meta[name*="description"]', 'content');
        $this->findFirstMatchingElement($element, $cw, 'imageUrl', 'meta[name="twitter:image"],meta[property="og:image"]', 'content');

        return $cw;
    }

    protected function findFirstMatchingElement(HtmlDomElement $element, CrawledWebsite $crawledWebsite, string $propertyName, string $selector, string $accessMethod): CrawledWebsite
    {
        $list = $element->find($selector);
        $item = array_pop($list);

        if ($item) {
            $setMethodName = sprintf('set%s', ucfirst($propertyName));
            $crawledWebsite->$setMethodName($item->$accessMethod);
        }

        return $crawledWebsite;
    }
}
