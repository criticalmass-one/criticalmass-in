<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\ProposalPersister;

use App\Criticalmass\MassTrackImport\TrackDecider\RideResult;
use App\Entity\Track;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProposalPersister implements ProposalPersisterInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var array $existentStravaActivityIds */
    protected $existentStravaActivityIds = [];

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function persist(RideResult $rideResult): RideResult
    {
        $user = $rideResult->getActivity()->getUser();

        $this->loadOldStravaActivityIds($user);

        $manager = $this->registry->getManager();

        if (!$rideResult->isMatch() ||
            !in_array($rideResult->getActivity()->getActivityId(), $this->existentStravaActivityIds)
        ) {
            $manager->persist($rideResult->getActivity());
        }

        $manager->flush();

        return $rideResult;
    }

    protected function loadOldStravaActivityIds(User $user): ProposalPersister
    {
        $tracks = $this->registry->getRepository(Track::class)->findByUser($user);

        /** @var Track $track */
        foreach ($tracks as $track) {
            if ($track->getStravaActivityId()) {
                $this->existentStravaActivityIds[] = $track->getStravaActivityId();
            }
        }

        $proposals = $this->registry->getRepository(TrackImportCandidate::class)->findByUser($user);

        /** @var TrackImportCandidate $proposal */
        foreach ($proposals as $proposal) {
            $this->existentStravaActivityIds[] = $proposal->getActivityId();
        }

        return $this;
    }
}