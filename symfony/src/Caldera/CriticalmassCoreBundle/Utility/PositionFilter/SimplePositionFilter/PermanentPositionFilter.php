<?php

namespace Caldera\CriticalmassCoreBundle\Utility\PositionFilter\SimplePositionFilter;

use Caldera\CriticalmassCoreBundle\Entity as Entity;
use Doctrine\ORM\Query\Expr;

class PermanentPositionFilter extends SimplePositionFilter
{
	/**
	 * {@inheritDoc}
	 */
	public function buildQuery($queryBuilder)
	{
		return $queryBuilder->innerJoin('p.user', 'u', Expr\Join::WITH, $queryBuilder->expr()->eq('u.id', 'p.user'));
	}
}