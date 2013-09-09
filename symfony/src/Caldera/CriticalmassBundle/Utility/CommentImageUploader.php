<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Diese Hilfsklasse verarbietet Fotos, die zu einem Kommentar hochgeladen wur-
 * den. Reduziert werden sowohl die eigentlichen Dimensionen des Bildes als
 * auch die Dateigroesse, um den Datentarif mobiler Smartphones nicht zu sehr
 * zu strapazieren.
 */
class CommentImageUploader
{
	/**
	 * Zugriff auf die Symfony-Umgebung, um beispielsweise Konfigurationsdaten
	 * lesen zu koennen.
	 */
	private $container;

	/**
	 * Zugriff auf den Security-Kontext, der unter anderem ein User-Objekt bereit-
	 * stellt.
	 */
	private $securityContext;

	/**
	 * Instanz des Bildes, das verarbeitet werden soll.
	 */
	private $commentImage;

	/**
	 * Quelltext der Bilddatei.
	 */
	private $imageFile;

	/**
	 * Beim Instanzieren dieser Klasse wird der SecurityContext und eine Implemen-
	 * tierung des ContainerInterface uebergeben, die in dieser Klasse verwendet
	 * werden.
	 *
	 * @param SecurityContext $securityContext
	 * @param ContainerInterface $container
	 */
	public function __construct(SecurityContext $securityContext, ContainerInterface $container)
	{
		$this->securityContext = $securityContext;
		$this->container = $container;
	}

	/**
	 * Speichert eine Instanz eines CommentImages.
	 *
	 * @param Entity\CommentImage $commentImage
	 *
	 * @return $this
	 */
	public function setCommentImage(Entity\CommentImage $commentImage)
	{
		$this->commentImage = $commentImage;

		return $this;
	}

	/**
	 * Gibt die gespeicherte Instanz eines CommentImage zurueck.
	 *
	 * @return CommentImage: Gespeichertes CommentImage
	 */
	public function getCommentImage()
	{
		return $this->commentImage;
	}

	/**
	 * Speichert den Quelltext der zu verarbeitenden Bilddatei.
	 *
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $imageFile: Instanz der Bilddatei
	 *
	 * @return $this
	 */
	public function setImageFile(\Symfony\Component\HttpFoundation\File\UploadedFile $imageFile)
	{
		$this->imageFile = $imageFile;

		return $this;
	}

	/**
	 * Diese Methode stoesst die eigentliche Verarbeitung des Bildes an. Die Enti-
	 * taet wird mit den entsprechenden Eigenschaften ausgestattet und anschlies-
	 * send abgespeichert.
	 *
	 * @return $this
	 */
	public function processUpload()
	{
		// Benutzer speichern
		$this->commentImage->setUser($this->securityContext->getToken()->getUser());

		// Zeitpunkt der Verarbeitung speichern
		$this->commentImage->setCreationDateTime(new \DateTime());

		// zufaelligen Namen erzeugen
		$this->commentImage->setName(md5(mt_rand()));

		// Dateipfad setzen
		$this->commentImage->setPath($this->commentImage->getName().'.'.$this->imageFile->guessExtension());

		// Bilddatei verschieben
		$this->imageFile->move($this->container->getParameter('commentimage.upload_filepath'), $this->commentImage->getPath());

		return $this;
	}
}