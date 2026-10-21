<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Track;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class TrackApproveControllerTest extends AbstractControllerTestCase
{
    private ?int $trackId = null;

    protected function tearDown(): void
    {
        if ($this->trackId !== null) {
            $em = $this->em();
            $track = $em->getRepository(Track::class)->find($this->trackId);
            $track->setReviewed(true);
            $em->flush();
        }

        parent::tearDown();
    }

    public function testGetIsNotAllowed(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'admin@criticalmass.in');
        $track = $this->unreviewedTrack();

        $client->request('GET', sprintf('/track/%d/approve', $track->getId()));

        $this->assertEquals(405, $client->getResponse()->getStatusCode());
        $this->assertFalse($this->reload($track)->isReviewed());
    }

    public function testPostWithoutTokenIsDenied(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'admin@criticalmass.in');
        $track = $this->unreviewedTrack();

        $client->request('POST', sprintf('/track/%d/approve', $track->getId()));

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertFalse($this->reload($track)->isReviewed());
    }

    public function testPostWithWrongTokenIsDenied(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'admin@criticalmass.in');
        $track = $this->unreviewedTrack();

        $client->request('POST', sprintf('/track/%d/approve', $track->getId()), ['_token' => 'wrong']);

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertFalse($this->reload($track)->isReviewed());
    }

    public function testPostWithTokenApprovesTrack(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'admin@criticalmass.in');
        $track = $this->unreviewedTrack();

        $client->request('POST', sprintf('/track/%d/approve', $track->getId()), [
            '_token' => $this->token($client, $track),
        ]);

        $this->assertResponseRedirects(sprintf('/track/view/%d', $track->getId()));
        $this->assertTrue($this->reload($track)->isReviewed());
    }

    public function testTrackViewOffersApprovalAsPostForm(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'admin@criticalmass.in');
        $track = $this->unreviewedTrack();

        $crawler = $client->request('GET', sprintf('/track/view/%d', $track->getId()));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $action = sprintf('/track/%d/approve', $track->getId());
        $this->assertSelectorNotExists(sprintf('a[href="%s"]', $action));
        $this->assertCount(1, $crawler->filter(sprintf('form[method="post"][action="%s"] input[name="_token"]', $action)));
    }

    private function unreviewedTrack(): Track
    {
        $em = $this->em();
        $track = $em->getRepository(Track::class)->findOneBy(['deleted' => false, 'enabled' => true]);
        $track->setReviewed(false);
        $em->flush();

        $this->trackId = $track->getId();

        return $track;
    }

    private function token(KernelBrowser $client, Track $track): string
    {
        $crawler = $client->request('GET', sprintf('/track/view/%d', $track->getId()));

        return (string) $crawler
            ->filter(sprintf('form[action="/track/%d/approve"] input[name="_token"]', $track->getId()))
            ->first()
            ->attr('value');
    }

    private function reload(Track $track): Track
    {
        $em = $this->em();
        $em->clear();

        return $em->getRepository(Track::class)->find($track->getId());
    }

    private function em(): EntityManagerInterface
    {
        return static::getContainer()->get('doctrine')->getManager();
    }
}
