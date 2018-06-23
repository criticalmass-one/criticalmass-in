<?php declare(strict_types=1);

namespace AppBundle\Controller\Track;

use AppBundle\Criticalmass\Statistic\RideEstimate\RideEstimateService;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Track;
use AppBundle\Traits\TrackHandlingTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class TrackManagementController extends AbstractController
{
    use TrackHandlingTrait;

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function listAction(Request $request, UserInterface $user, PaginatorInterface $paginator)
    {
        $query = $this->getTrackRepository()->findByUserQuery($user);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('AppBundle:Track:list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Security("is_granted('edit', track)")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function downloadAction(Request $request, UserInterface $user, Track $track): Response
    {
        $trackContent = file_get_contents($this->getTrackFilename($track));

        $response = new Response();

        $response->headers->add([
            'Content-disposition' => 'attachment; filename=track.gpx',
            'Content-type',
            'text/plain',
        ]);

        $response->setContent($trackContent);

        return $response;
    }

    /**
     * @Security("is_granted('edit', track)")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function toggleAction(Request $request, UserInterface $user, Track $track, RideEstimateService $rideEstimateService): Response
    {
        $track->setEnabled(!$track->getEnabled());

        $this->getManager()->flush();

        $rideEstimateService->calculateEstimates($track->getRide());

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

    /**
     * @Security("is_granted('edit', track)")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function deleteAction(Request $request, UserInterface $user, Track $track, RideEstimateService $rideEstimateService): Response
    {
        $track->setDeleted(true);

        $this->getManager()->flush();

        $rideEstimateService->calculateEstimates($track->getRide());

        return $this->redirect($this->generateUrl('caldera_criticalmass_track_list'));
    }

    protected function getTrackFilename(Track $track): string
    {
        $rootDirectory = $this->getParameter('kernel.root_dir');
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $filename = $helper->asset($track, 'trackFile');

        return $rootDirectory . '/../web' . $filename;
    }
}
