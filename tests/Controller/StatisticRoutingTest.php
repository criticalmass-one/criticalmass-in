<?php declare(strict_types=1);

namespace Tests\Controller;

class StatisticRoutingTest extends AbstractControllerTestCase
{
    public function testStatisticShowsTheOverview(): void
    {
        $client = static::createClient();

        $client->request('GET', '/statistic');

        self::assertResponseIsSuccessful();
        self::assertRouteSame('caldera_criticalmass_statistic_overview');
    }

    public function testMonthListKeepsItsRoute(): void
    {
        $client = static::createClient();

        $client->request('GET', '/statistic/2026/9');

        self::assertResponseIsSuccessful();
        self::assertRouteSame('caldera_criticalmass_statistic_ride_month');
    }
}
