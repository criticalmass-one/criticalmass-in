<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Ride;
use App\Entity\Subride;
use App\Repository\RideRepository;

/**
 * Mini-Masses kopieren, wenn es nichts zu kopieren gibt.
 *
 * `RideRepository::getPreviousRideWithSubrides()` endet auf
 * `getOneOrNullResult()` — in einer Stadt, in der noch nie Mini-Masses
 * eingetragen wurden, gibt es keine fruehere Tour, und die Methode liefert
 * null. Das Template rechnete nicht damit und rief `object_path(null)` auf.
 *
 * Getroffen hat das echte Menschen: Am 06.09.2026 um 17:20 Uhr bekam jemand
 * aus Koeln auf `/koeln/2026-09-27/preparecopysubrides` einen 500er, direkt
 * von der Tourenseite kommend. Ein zweiter Fall liegt am 30.08.2026.
 *
 * Es ist dieselbe Familie wie der Fotofehler in #1522 — ein Controller, der
 * null durchreicht, und ein Template, das es nicht erwartet.
 */
class SubrideCopyWithoutPreviousRideTest extends AbstractControllerTestCase
{
    /**
     * Der Fall des Koelner Nutzers: nichts zu kopieren.
     *
     * Die Lage wird hergestellt statt gesucht — eine Tour, vor der es in ihrer
     * Stadt ueberhaupt keine gibt. Ein Test, der sich eine passende Tour aus
     * den Fixtures sucht, haengt sonst davon ab, was andere Tests vorher in
     * die Datenbank geschrieben haben; genau daran ist die erste Fassung im
     * Suitenlauf gescheitert, waehrend sie allein gruen war.
     */
    public function testThePageExplainsItselfWhenThereIsNothingToCopy(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $stadt = $entityManager->getRepository(\App\Entity\City::class)->findOneBy([]);
        self::assertNotNull($stadt, 'Die Fixtures liefern mindestens eine Stadt.');

        $erste = new Ride();
        $erste->setCity($stadt);
        $erste->setTitle('Die allererste Tour');
        $erste->setDateTime(new \DateTime('1990-01-01 18:00'));
        $erste->setCreatedAt(new \DateTime());
        $erste->setUpdatedAt(new \DateTime());
        $erste->setEnabled(true);
        $entityManager->persist($erste);
        $entityManager->flush();

        self::assertNull(
            static::getContainer()->get(RideRepository::class)->getPreviousRideWithSubrides($erste),
            'Vor der allerersten Tour kann es keinen Vorgaenger geben.'
        );

        $this->loginAs($client, $this->irgendeineEmail($entityManager));

        $client->request('GET', sprintf(
            '/%s/%s/preparecopysubrides',
            $stadt->getMainSlugString(),
            $erste->getDateTime()->format('Y-m-d')
        ));

        self::assertResponseIsSuccessful('Auch ohne Vorgaenger hat die Seite eine Antwort.');
        self::assertStringContainsString(
            'noch keine frühere Tour mit Mini-Masses',
            (string) $client->getResponse()->getContent(),
            'Und sagt, warum nichts zu holen ist.'
        );

        $entityManager->remove($erste);
        $entityManager->flush();
    }

    private function irgendeineEmail(\Doctrine\ORM\EntityManagerInterface $entityManager): string
    {
        $nutzer = $entityManager->getRepository(\App\Entity\User::class)->findOneBy([]);
        self::assertNotNull($nutzer, 'Die Fixtures liefern mindestens einen Nutzer.');

        return (string) $nutzer->getEmail();
    }

    /**
     * Der gute Fall bleibt: Gibt es einen Vorgaenger mit Mini-Masses, nennt
     * die Seite ihn beim Namen.
     *
     * Der Test legt sich diese Lage selbst an, statt sie in den Fixtures zu
     * suchen — sonst haette er sich uebersprungen, sobald die Fixtures sie
     * nicht mehr hergeben, und dabei gruen ausgesehen.
     */
    public function testTheRideToCopyFromIsNamedWhenThereIsOne(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $stadt = $entityManager->getRepository(\App\Entity\City::class)->findOneBy([]);
        self::assertNotNull($stadt, 'Die Fixtures liefern mindestens eine Stadt.');

        $vorgaenger = new Ride();
        $vorgaenger->setCity($stadt);
        $vorgaenger->setTitle('Tour mit Mini-Masses');
        $vorgaenger->setDateTime(new \DateTime('-2 months'));
        $vorgaenger->setCreatedAt(new \DateTime());
        $vorgaenger->setUpdatedAt(new \DateTime());
        $vorgaenger->setEnabled(true);
        $entityManager->persist($vorgaenger);

        $minimass = new Subride();
        $minimass->setRide($vorgaenger);
        $minimass->setTitle('Nordroute');
        $minimass->setDateTime(new \DateTime('-2 months'));
        $entityManager->persist($minimass);

        $spaeter = new Ride();
        $spaeter->setCity($stadt);
        $spaeter->setTitle('Tour ohne Mini-Masses');
        $spaeter->setDateTime(new \DateTime('+2 months'));
        $spaeter->setCreatedAt(new \DateTime());
        $spaeter->setUpdatedAt(new \DateTime());
        $spaeter->setEnabled(true);
        $entityManager->persist($spaeter);

        $entityManager->flush();

        $this->loginAs($client, $this->irgendeineEmail($entityManager));

        $client->request('GET', sprintf(
            '/%s/%s/preparecopysubrides',
            $stadt->getMainSlugString(),
            $spaeter->getDateTime()->format('Y-m-d')
        ));

        self::assertResponseIsSuccessful();

        // Welche Tour die Abfrage tatsaechlich waehlt, entscheidet sie selbst:
        // Es ist die juengste vor dieser, und das kann auch eine aus den
        // Fixtures sein. Geprueft wird deshalb gegen ihre Wahl, nicht gegen
        // einen geratenen Titel.
        $gewaehlt = static::getContainer()->get(RideRepository::class)
            ->getPreviousRideWithSubrides($spaeter);
        self::assertNotNull($gewaehlt, 'Jetzt gibt es einen Vorgaenger mit Mini-Masses.');

        $inhalt = (string) $client->getResponse()->getContent();
        self::assertStringContainsString('können die folgenden Mini-Masses kopiert werden', $inhalt);
        self::assertStringContainsString((string) $gewaehlt->getTitle(), $inhalt);

        $entityManager->remove($minimass);
        $entityManager->remove($vorgaenger);
        $entityManager->remove($spaeter);
        $entityManager->flush();
    }
}
