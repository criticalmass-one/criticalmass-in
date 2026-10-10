<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Participation;
use App\Entity\Ride;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class ProfileParticipationControllerTest extends AbstractControllerTestCase
{
    private KernelBrowser $client;
    private ?int $participationId = null;
    private ?int $rideId = null;
    /** @var array{int, int, int} */
    private array $rideCounters = [0, 0, 0];

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $em = $this->getEntityManager();

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

        $this->rideId = $ride->getId();
        $this->rideCounters = [
            $ride->getParticipationsNumberYes(),
            $ride->getParticipationsNumberMaybe(),
            $ride->getParticipationsNumberNo(),
        ];

        $participation = (new Participation())
            ->setRide($ride)
            ->setUser($this->getUser('testuser@criticalmass.in'))
            ->setGoingYes(false)
            ->setGoingMaybe(true)
            ->setGoingNo(false);

        $em->persist($participation);
        $em->flush();

        $this->participationId = $participation->getId();
        $em->clear();
    }

    protected function tearDown(): void
    {
        $em = $this->getEntityManager();
        $em->clear();

        $participation = $em->find(Participation::class, $this->participationId);

        if ($participation) {
            $em->remove($participation);
        }

        $ride = $em->find(Ride::class, $this->rideId);

        if ($ride) {
            [$yes, $maybe, $no] = $this->rideCounters;
            $ride
                ->setParticipationsNumberYes($yes)
                ->setParticipationsNumberMaybe($maybe)
                ->setParticipationsNumberNo($no);
        }

        $em->flush();

        parent::tearDown();
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return static::getContainer()->get('doctrine')->getManager();
    }

    private function findParticipation(): ?Participation
    {
        $em = $this->getEntityManager();
        $em->clear();

        return $em->find(Participation::class, $this->participationId);
    }

    private function storeCsrfToken(string $tokenId, string $tokenValue): void
    {
        $this->client->request('GET', '/');
        $session = $this->client->getRequest()->getSession();
        $session->set('_csrf/' . $tokenId, $tokenValue);
        $session->save();
    }

    public function testUpdateViaGetIsRejected(): void
    {
        $this->loginAs($this->client, 'testuser@criticalmass.in');

        $this->client->request('GET', sprintf('/profile/participation/%d/update?status=no', $this->participationId));

        $this->assertSame(405, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->findParticipation()->getGoingMaybe());
    }

    public function testDeleteViaGetIsRejected(): void
    {
        $this->loginAs($this->client, 'testuser@criticalmass.in');

        $this->client->request('GET', sprintf('/profile/participation/%d/delete', $this->participationId));

        $this->assertSame(405, $this->client->getResponse()->getStatusCode());
        $this->assertNotNull($this->findParticipation());
    }

    public function testUpdateWithoutValidTokenIsRejected(): void
    {
        $this->loginAs($this->client, 'testuser@criticalmass.in');

        $this->client->request('POST', sprintf('/profile/participation/%d/update', $this->participationId), [
            'status' => 'no',
            '_token' => 'invalid',
        ]);

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->findParticipation()->getGoingMaybe());
    }

    public function testDeleteWithoutValidTokenIsRejected(): void
    {
        $this->loginAs($this->client, 'testuser@criticalmass.in');

        $this->client->request('POST', sprintf('/profile/participation/%d/delete', $this->participationId), [
            '_token' => 'invalid',
        ]);

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
        $this->assertNotNull($this->findParticipation());
    }

    public function testUpdateWithValidToken(): void
    {
        $this->loginAs($this->client, 'testuser@criticalmass.in');
        $this->storeCsrfToken('participation_update_' . $this->participationId, 'test_csrf_token');

        $this->client->request('POST', sprintf('/profile/participation/%d/update', $this->participationId), [
            'status' => 'no',
            '_token' => 'test_csrf_token',
        ]);

        $this->assertResponseRedirects('/profile/participation/list');

        $participation = $this->findParticipation();
        $this->assertTrue($participation->getGoingNo());
        $this->assertFalse($participation->getGoingMaybe());
        $this->assertFalse($participation->getGoingYes());
    }

    public function testDeleteWithValidToken(): void
    {
        $this->loginAs($this->client, 'testuser@criticalmass.in');
        $this->storeCsrfToken('participation_delete_' . $this->participationId, 'test_csrf_token');

        $this->client->request('POST', sprintf('/profile/participation/%d/delete', $this->participationId), [
            '_token' => 'test_csrf_token',
        ]);

        $this->assertResponseRedirects('/profile/participation/list');
        $this->assertNull($this->findParticipation());
    }
}
