<?php

namespace Caldera\CriticalmassGalleryBundle\Controller;

use Caldera\CriticalmassGalleryBundle\Utility\ExifReader;
use Caldera\CriticalmassStatisticBundle\Utility\Trackable;
use Caldera\CriticalmassGalleryBundle\Utility\ExifGpsReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller implements Trackable
{
    public function indexAction($name)
    {
        $filename = "/Applications/XAMPP/htdocs/criticalmass/symfony/web/IMG_9707.jpg";

        $exif = exif_read_data($filename);

        $er = new ExifReader();

        print_r($er->getLens());

        return new Response();
    }
}
