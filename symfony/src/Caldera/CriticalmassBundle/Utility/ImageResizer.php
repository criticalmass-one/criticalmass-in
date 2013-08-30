<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

/**
 * Diese Hilfsklasse befasst sich mit der Verkleinerung von Bildern, um sie
 * beispielsweise als Vorschaubild anzeigen zu koennen.
 */
class ImageResizer
{
	/**
	 * Variable fuer den Container, der ueber den Service injiziert wird.
	 */
	private $container;

	/**
	 * Instanz einer CommentImage-Entitaet.
	 */
	private $commentImage;

	/**
	 * Instanz eines GD-Bildes-
	 */
	private $image;

	/**
	 * Der Service-Container wird automatisch mit in den Konstruktor eingebaut.
	 *
	 * @param \appDevDebugProjectContainer $container
	 */
	public function __construct(\appDevDebugProjectContainer $container)
	{
		$this->container = $container;
	}

	/**
	 * Legt das korrespondierende CommentImage fest und erzeugt daraus ein GD-
	 * Bild, das im weiteren Verlauf bearbeitet wird.
	 *
	 * @param Entity\CommentImage $commentImage: CommentImage-Entitaet
	 *
	 * @return ImageResizer: $this
	 */
	public function setCommentImage(Entity\CommentImage $commentImage)
	{
		$this->commentImage = $commentImage;

		// Bild aus JPEG-Datei erzeugen
		$this->image = imagecreatefromjpeg($this->container->getParameter('commentimage.upload_filepath').$this->commentImage->getPath());

		return $this;
	}

	/**
	 * Gibt das abgespeicherte CommentImage zurueck.
	 *
	 * @return CommentImage: Dazugehoerige CommentImage-Instanz
	 */
	public function getCommentImage()
	{
		return $this->commentImage;
	}

	/**
	 * Stoesst die Verkleinerung des Bildes an. Dazu wird die laengste Seite des
	 * Bildes auf die entsprechend konfigurierte Laenge aus den Einstellungen ver-
	 * kleinert.
	 *
	 * @return ImageResizer: $this
	 */
	public function resize()
	{
		$this->resizeLongSideToLength($this->container->getParameter('commentimage.upload_longside'));

		return $this;
	}

	/**
	 * Diese Methode verkleinert die laengste Seite des Bildes auf die im Parame-
	 * ter uebergebene Laenge. Zunaechst werden anhand des Parameters die neuen
	 * Dimensionen des Bildes berechnet und anschliessend an die Methode
	 * resizeTo() delegiert.
	 * 
	 * @param Integer $length: Neue Laenge der laengsten Seite
	 */
	public function resizeLongSideToLength($length)
	{
		// ist das Bild breiter als hoch?
		if (imagesx($this->image) > imagesy($this->image))
		{
			$longSide = imagesx($this->image);
		}
		// oder hoeher als breit?
		else
		{
			$longSide = imagesy($this->image);
		}

		$resizeFactor = $length / $longSide;

		$this->resizeTo(imagesx($this->image) * $resizeFactor, imagesy($this->image) * $resizeFactor);
	}

	public function resizeTo($width, $height)
	{
		$resizedImage = imagecreatetruecolor($width, $height);

		imagecopyresized($resizedImage, $this->image, 0, 0, 0, 0, $width, $height, imagesx($this->image), imagesy($this->image));

		$this->image = $resizedImage;

		$this->commentImage->setResizedWidth(imagesx($this->image));
		$this->commentImage->setResizedHeight(imagesy($this->image));
	}

	public function save()
	{
		imagejpeg($this->image,
							$this->container->getParameter('commentimage.upload_filepath').$this->commentImage->getName().'-'.imagesx($this->image).'x'.imagesy($this->image).'.jpeg',
							$this->container->getParameter('commentimage.upload_quality'));

		return $this;
	}

	public function getResizedPath()
	{
		return $this->container->getParameter('commentimage.upload_filepath').$this->commentImage->getId().'-'.imagesx($this->image).'x'.imagesy($this->image).'.jpeg';
	}

	public function __destruct()
	{
		imagedestroy($this->image);
	}
}
