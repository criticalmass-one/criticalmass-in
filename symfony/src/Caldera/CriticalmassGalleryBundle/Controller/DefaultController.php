<?php

namespace Caldera\CriticalmassGalleryBundle\Controller;

use Caldera\CriticalmassGalleryBundle\Entity\Image;
use Caldera\CriticalmassGalleryBundle\Utility\ExifReader;
use Caldera\CriticalmassGalleryBundle\Utility\ImageExifWriter;
use Caldera\CriticalmassGalleryBundle\Utility\ImageImporter;
use Caldera\CriticalmassStatisticBundle\Utility\Trackable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller implements Trackable
{
    public function indexAction($name)
    {
        $dir = ".";

        $ii = new ImageImporter($this->getDoctrine());
        $ii->setRide($this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findOneById(2));
        $ii->setUser($this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:User')->findOneById(1));
        $ii->setDirectory($dir)->fetchImageFiles()->execute();

        return new Response();
    }

    public function displayimageAction($id)
    {
        $image = $this->getDoctrine()->getRepository('CalderaCriticalmassGalleryBundle:Image')->findOneById($id);

        $imageString = file_get_contents("/Applications/XAMPP/htdocs/criticalmass/symfony/web/images/".$image->getId().".jpg");

        $response = new Response();
        $response->setContent($imageString);
        $response->headers->set("Content-Type", "image/jpg");

        return $response;
    }
}
