<?php

namespace Caldera\CriticalmassBundle\Utility\MapBuilder\MapBuilderHelper;

use Caldera\CriticalmassBundle\Utility\PositionArray as PositionArray;

abstract class BaseMapBuilderHelper
{
	protected $positionArray;

	public function __construct(PositionArray $positionArray)
	{
		$this->positionArray = $positionArray;
	}
}