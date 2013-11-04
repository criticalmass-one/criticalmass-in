<?php

namespace Caldera\CriticalmassGalleryBundle\Controller;

use Caldera\CriticalmassGalleryBundle\Entity\Image;
use Caldera\CriticalmassGalleryBundle\Utility\ExifReader;
use Caldera\CriticalmassGalleryBundle\Utility\ImageExifWriter;
use Caldera\CriticalmassStatisticBundle\Utility\Trackable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller implements Trackable
{
    public function indexAction($name)
    {
        $filename = "/Applications/XAMPP/htdocs/criticalmass/symfony/web/IMG_9707.jpg";
        $image = new Image();

        $iew = new ImageExifWriter($image);
        $image = $iew->readExifFromFilename($filename);

        return new Response();
    }
}
