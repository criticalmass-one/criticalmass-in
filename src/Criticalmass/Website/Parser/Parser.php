<?php declare(strict_types=1);

namespace App\Criticalmass\Website\Parser;

use App\Entity\CrawledWebsite;
use GuzzleHttp\Psr7\Request;
use \simplehtmldom_1_5\simple_html_dom as HtmlDomElement;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class Parser implements ParserInterface
{
    public function parse(string $url): ?CrawledWebsite
    {
        if ($html = $this->fetchHtml($url)) {
            $crawledWebsite = $this->parseHtml($html, $url);

            return $crawledWebsite;
        }

        return null;
    }

    protected function fetchHtml(string $url): ?HtmlDomElement
    {
        $config = [
            'timeout' => 10,
        ];

        $adapter = GuzzleAdapter::createWithConfig($config);

        $request = new Request('GET', $url);

        try {
            $response = $adapter->sendRequest($request);
        } catch (\Exception $exception) {
            return null;
        }

        if (200 === $response->getStatusCode()) {
            $htmlString = $response->getBody()->getContents();

            try {
                $htmlDomElement = \Sunra\PhpSimple\HtmlDomParser::str_get_html($htmlString);
            } catch (\Exception $exception) {
                return null;
            }

            if ($htmlDomElement === false) {
                return null;
            }

            return $htmlDomElement;
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
