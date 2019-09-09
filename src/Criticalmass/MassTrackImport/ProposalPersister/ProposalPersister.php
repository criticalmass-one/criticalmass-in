<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\ProposalPersister;

use App\Entity\Track;
use App\Entity\TrackImportProposal;
use App\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProposalPersister implements ProposalPersisterInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var array $existentStravaActivityIds */
    protected $existentStravaActivityIds = [];

    /** @var TokenStorageInterface $tokenStorage */
    protected $tokenStorage;

    public function __construct(RegistryInterface $registry, TokenStorageInterface $tokenStorage)
    {
        $this->registry = $registry;
        $this->tokenStorage = $tokenStorage;
    }

    public function persist(array $proposalList): array
    {
        $this->loadOldStravaActivityIds();

        $manager = $this->registry->getManager();

        foreach ($proposalList as $stravaActivityId => $proposal) {
            if (!in_array($stravaActivityId, $this->existentStravaActivityIds)) {
                $this->persist($proposal);
            } else {
                unset($proposalList[$stravaActivityId]);
            }
        }

        $manager->flush();

        return $proposalList;
    }

    protected function getUser(): User
    {
        return $this->tokenStorage->getToken()->getUser();
    }

    protected function loadOldStravaActivityIds(): ProposalPersister
    {
        $tracks = $this->registry->getRepository(Track::class)->findByUser($this->getUser());

        /** @var Track $track */
        foreach ($tracks as $track) {
            if ($track->getStravaActivityId()) {
                $this->existentStravaActivityIds[] = $track->getStravaActivityId();
            }
        }

        $proposals = $this->registry->getRepository(TrackImportProposal::class)->findByUser($this->getUser());

        /** @var TrackImportProposal $proposal */
        foreach ($proposals as $proposal) {
            $this->existentStravaActivityIds[] = $track->getStravaActivityId();
        }

        return $this;
    }
}