<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Entity\Track;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;

class TrackController extends AbstractController
{
    /**
     * @Security("is_granted('view', track)")
     * @ParamConverter("track", class="App:Track", options={"id" = "trackId"})
     */
    public function viewAction(Track $track): Response
    {
        return $this->render('Track/view.html.twig', [
            'track' => $track,
        ]);
    }

    /**
     * @Security("is_granted('approve', track)")
     * @ParamConverter("track", class="App:Track", options={"id" = "trackId"})
     */
    public function approveAction(Track $track, RegistryInterface $registry): Response
    {
        $track->setReviewed(true);

        $registry->getManager()->flush();

        return $this->redirectToRoute('caldera_criticalmass_track_view', [
            'trackId' => $track->getId(),
        ]);
    }
}
