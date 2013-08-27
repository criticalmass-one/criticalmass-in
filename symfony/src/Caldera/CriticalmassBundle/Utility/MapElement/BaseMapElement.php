<?php

namespace Caldera\CriticalmassBundle\Utility\MapElement;

/**
 * Basis-Klasse zur Repräsentierung eines grafischen Elementes in der einge-
 * betteten Karte. Alle grafischen Elemente werden aus dieser Klasse gebildet
 * und muessen die Methoden getId() und draw() implementieren.
 */
abstract class BaseMapElement
{
	/**
	 * Diese Methode muss eine eindeutige und einmalige ID fuer die Identifizier-
	 * ung des Elementes zurueckgeben. Idealerweise wird die ID aus den Eigen-
	 * schaften des Elementes gebildet.
	 *
	 * @return String: Eindeutige Kennzeichnung des Elementes
	 */
	public abstract function getId();

	/**
	 * Die Methode draw() gibt ein Array zurueck, mit deren Werten das JavaScript
	 * auf der Client-Seite das Element in die eingebettete Karte einfuegen kann.
	 *
	 * @return Array: Werte des grafischen Elementes
	 */
	public abstract function draw();
}