<?php declare(strict_types=1);

namespace App\Criticalmass\CityPopulationFetcher;

use App\Criticalmass\CityPopulationFetcher\Exception\CityNotFoundException;
use App\Criticalmass\CityPopulationFetcher\Exception\ValueNotFoundException;
use App\Criticalmass\CityPopulationFetcher\Exception\ValueNotParseableException;
use Curl\Curl;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

class WikipediaDeCityPopulationFetcher implements CityPopulationFetcherInterface
{
    const URL_PATTERN = 'https://de.wikipedia.org/wiki/%s';
    const POPULATION_KEYWORD = 'Einwohner';

    public function fetch(string $cityName): ?int
    {
        $url = $this->buildUrl($cityName);

        $html = $this->getHtml($url);

        if (!$html) {
            throw new CityNotFoundException(sprintf('City "%s" not found in Wikipedia. Looked up "%s".', $cityName, $url));
        }

        $populationNumber = $this->parseHtml($html);

        if (!$populationNumber) {
            throw new ValueNotFoundException(sprintf('No useable population data found for city "%s" in Wikipedia', $cityName), Response::HTTP_NOT_FOUND);
        }

        return $this->convertNumber($populationNumber);
    }

    protected function buildUrl(string $cityName): string
    {
        return sprintf(self::URL_PATTERN, $cityName);
    }

    protected function getHtml(string $url): ?string
    {
        $curl = new Curl();

        $curl->get($url);

        if ($curl->httpStatusCode !== Response::HTTP_OK) {
            return null;
        }

        return $curl->response;
    }

    protected function parseHtml(string $html): ?string
    {
        $crawler = new Crawler($html);

        // find table cell with "Einwohner" label
        $crawler = $crawler
            ->filter('table tr td')
            ->reduce(function (Crawler $node, $i) {
                return (strpos($node->text(), self::POPULATION_KEYWORD) === 0);
            });

        try {
            // here we have the number
            return $crawler->nextAll()->first()->text();
        } catch (\Exception $exception) {
            return null;
        }
    }

    protected function convertNumber(string $populationNumber): int
    {
        try {
            return (int) str_replace('.', '', (explode(' ', $populationNumber)[0]));
        } catch (\Exception $exception) {
            throw new ValueNotParseableException(sprintf('Value %s could not be parsed into integer', $populationNumber));
        }
    }
}