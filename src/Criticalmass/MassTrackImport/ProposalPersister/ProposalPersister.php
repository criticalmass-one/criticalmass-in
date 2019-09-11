<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\ProposalPersister;

use App\Criticalmass\MassTrackImport\TrackDecider\RideResult;
use App\Entity\Track;
use App\Entity\TrackImportCandidate;
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

    public function persist(array $rideResultList): array
    {
        $this->loadOldStravaActivityIds();

        $manager = $this->registry->getManager();

        /**
         * @var int $stravaActivityId
         * @var RideResult $rideResult
         */
        foreach ($rideResultList as $stravaActivityId => $rideResult) {
            if (!$rideResult->isMatch() ||
                !in_array($stravaActivityId, $this->existentStravaActivityIds)
            ) {
                $manager->persist($rideResult->getActivity());
            } else {
                unset($rideResultList[$stravaActivityId]);
            }
        }

        $manager->flush();

        return $rideResultList;
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

        $proposals = $this->registry->getRepository(TrackImportCandidate::class)->findByUser($this->getUser());

        /** @var TrackImportCandidate $proposal */
        foreach ($proposals as $proposal) {
            $this->existentStravaActivityIds[] = $proposal->getActivityId();
        }

        return $this;
    }
}