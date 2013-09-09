<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter\SimplePositionFilter;

use Caldera\CriticalmassBundle\Utility\PositionFilter\BasePositionFilter;

/**
 * Abstrakte Klasse eines einfachen Filters. Ein einfacher Filter muss ledig-
 * lich die Methode buildQuery implementieren, in der er Details zu seiner Ab-
 * frage an den Doctrine-QueryBuilder anhaengt.
 */
abstract class SimplePositionFilter extends BasePositionFilter
{
	/**
	 * In dieser Methode wird der QueryBuilder um die eigenen Parameter erweitert.
	 */
	public abstract function buildQuery($queryBuilder);
}