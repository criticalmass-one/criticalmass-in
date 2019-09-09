<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\ProposalPersister;

interface ProposalPersisterInterface
{
    public function persist(array $proposalList): array;
}