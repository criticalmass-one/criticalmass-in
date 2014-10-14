<?php

namespace Caldera\CriticalmassGalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caldera\CriticalmassGalleryBundle\Entity\Photos;
use Symfony\Component\HttpFoundation\Request;

class PhotosController extends Controller
{
    public function listAction() {
        $photos = $this->getDoctrine()->getRepository('CriticalmassGalleryBundle:Photos')->findBy(array('enabled' => true));
        return $this->render('CriticalmassGalleryBundle:Default:index.html.twig', array('photos' => $photos));
    }

    public function addAction(Request $request) {
        $photos = new Photos();
        $form = $this->createFormBuilder($photos)
            ->setAction($this->generateUrl('criticalmass_gallery_photos_add'))
            ->add('file')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $photos->handleUpload();

            $photos->setUser($this->getUser());
            $photos->setDescription("");

            $em->persist($photos);
            $em->flush();

            return $this->redirect($this->generateUrl('caldera_criticalmass_statistic_track_list'));
        }

        return $this->render('CriticalmassGalleryBundle:Default:add.html.twig', array('form' => $form->createView()));
    }
}
