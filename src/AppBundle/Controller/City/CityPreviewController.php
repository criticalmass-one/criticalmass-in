<?php declare(strict_types=1);

namespace AppBundle\Controller\City;

use AppBundle\Entity\City;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CityPreviewController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("city", class="AppBundle:City")
     */
    public function selectAction(City $city): Response
    {
        return $this->render('AppBundle:CityPreview:select.html.twig', [
            'city' => $city,
        ]);
    }
}
