<?php declare(strict_types=1);

namespace Tests\Controller\Ride;

use App\Entity\Ride;
use Tests\Controller\AbstractControllerTestCase;

class TimelapseControllerTest extends AbstractControllerTestCase
{
    public function testTimelapsePageWithoutTemplateIsGone(): void
    {
        $client = static::createClient();
        // Unbekannte Pfade laufen erst ueber die Schraegstrich-Umleitung, dann in den 404.
        $client->followRedirects();

        $em = static::getContainer()->get('doctrine')->getManager();
        $ride = $em->getRepository(Ride::class)
            ->createQueryBuilder('r')
            ->join('r.city', 'c')
            ->join('c.mainSlug', 'cs')
            ->where('cs.slug = :citySlug')
            ->setParameter('citySlug', 'hamburg')
            ->orderBy('r.dateTime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        $this->assertNotNull($ride, 'Hamburg ride fixture should exist');

        $client->request('GET', sprintf(
            '/%s/%s/timelapse',
            $ride->getCity()->getMainSlugString(),
            $ride->getDateTime()->format('Y-m-d')
        ));

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
