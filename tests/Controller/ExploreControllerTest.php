<?php declare(strict_types=1);

namespace Tests\Controller;

/**
 * Das Popup der Entdecken-Karte wird im Browser per Handlebars aus den Feldern
 * der Staedte-API gefuellt. Der Test legt beides nebeneinander.
 */
class ExploreControllerTest extends AbstractControllerTestCase
{
    public function testPopupLinksToTheCityPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/explore');
        $this->assertResponseIsSuccessful();

        $html = $client->getResponse()->getContent();

        self::assertSame(1, preg_match('#<template id="city-popup">(.*?)</template>#s', $html, $templateMatch), 'Popup-Template fehlt.');
        self::assertSame(1, preg_match('#href="([^"]*)"#', $templateMatch[1], $hrefMatch), 'Popup-Link fehlt.');
        self::assertSame(1, preg_match('#data-map--explore-map-api-query-value="([^"]+)"#', $html, $apiMatch), 'API-Adresse fehlt.');

        $client->request('GET', html_entity_decode($apiMatch[1]));
        $this->assertResponseIsSuccessful();

        $cityList = json_decode($client->getResponse()->getContent(), true);
        self::assertNotEmpty($cityList);

        $hamburg = current(array_filter($cityList, fn (array $city): bool => 'Hamburg' === $city['name']));
        self::assertIsArray($hamburg);

        // Platzhalter so aufloesen, wie Handlebars es tut: fehlende Felder werden leer.
        $href = preg_replace_callback('#\{\{\s*([\w.]+)\s*\}\}#', function (array $placeholder) use ($hamburg): string {
            $value = $hamburg;

            foreach (explode('.', $placeholder[1]) as $key) {
                $value = is_array($value) && array_key_exists($key, $value) ? $value[$key] : '';
            }

            return (string) $value;
        }, $hrefMatch[1]);

        self::assertSame('/hamburg', $href);
    }
}
