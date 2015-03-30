<?php

namespace Caldera\CriticalmassGalleryBundle\Controller;

use Caldera\CriticalmassGalleryBundle\Entity\Photo;
use Caldera\CriticalmassGalleryBundle\Utility\PhotoUploader\PhotoUploader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UploadController extends Controller
{
    public function processAction(Request $request, $citySlug, $rideDate)
    {
        $citySlugObj = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug);

        if (!$citySlugObj)
        {
            throw new NotFoundHttpException('Wir haben leider keine Stadt in der Datenbank, die sich mit '.$citySlug.' identifiziert.');
        }

        $city = $citySlugObj->getCity();

        try {
            $rideDateTime = new \DateTime($rideDate);
        }
        catch (\Exception $e)
        {
            throw new NotFoundHttpException('Mit diesem Datum können wir leider nichts anfange. Bitte gib ein Datum im Format YYYY-MM-DD an.');
        }

        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findCityRideByDate($city, $rideDateTime);

        if (!$ride)
        {
            throw new NotFoundHttpException('Wir haben leider keine Tour in '.$city->getCity().' am '.$rideDateTime->format('d. m. Y').' gefunden.');
        }
        
        $photo = new Photo();
        $photo->setCity($city);
        $photo->setRide($ride);

        if (($request->getMethod() == 'POST') &&
            (strtolower($request->files->get('file')->getClientOriginalExtension()) == "jpg"))
        {
            $em = $this->getDoctrine()->getManager();

            $photo->setFile($request->files->get('file'));
            $photo->setUser($this->getUser());

            $em->persist($photo);
            $em->flush();

            $pu = new PhotoUploader();
            $pu->setPhoto($photo);
            $pu->execute();

         //   return $this->redirect($this->generateUrl('caldera_criticalmass_gallery_photos_index'));
        }
        
        return new Response('qwdqwdw');
    }
    
    public function uploadAction(Request $request, $citySlug, $rideDate) {
        $citySlugObj = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug);

        if (!$citySlugObj)
        {
            throw new NotFoundHttpException('Wir haben leider keine Stadt in der Datenbank, die sich mit '.$citySlug.' identifiziert.');
        }

        $city = $citySlugObj->getCity();

        try {
            $rideDateTime = new \DateTime($rideDate);
        }
        catch (\Exception $e)
        {
            throw new NotFoundHttpException('Mit diesem Datum können wir leider nichts anfange. Bitte gib ein Datum im Format YYYY-MM-DD an.');
        }

        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findCityRideByDate($city, $rideDateTime);

        if (!$ride)
        {
            throw new NotFoundHttpException('Wir haben leider keine Tour in '.$city->getCity().' am '.$rideDateTime->format('d. m. Y').' gefunden.');
        }

        return $this->render('CalderaCriticalmassGalleryBundle:Upload:upload.html.twig', array('ride' => $ride));
    }
}
