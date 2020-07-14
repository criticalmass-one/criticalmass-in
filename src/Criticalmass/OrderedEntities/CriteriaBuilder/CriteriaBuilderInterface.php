<?php declare(strict_types=1);

namespace App\Criticalmass\OrderedEntities\CriteriaBuilder;

use App\Criticalmass\OrderedEntities\OrderedEntityInterface;
use Doctrine\Common\Collections\Criteria;

interface CriteriaBuilderInterface
{
    public function build(OrderedEntityInterface $orderedEntity, string $direction): Criteria;
}