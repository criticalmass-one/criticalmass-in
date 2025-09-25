<?php declare(strict_types=1);

namespace App\Controller\City;

use App\Controller\AbstractController;
use App\Entity\City;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class CityHeaderImageController extends AbstractController
{
    /**
     * @ParamConverter("city", class="App:City")
     * @Security("has_role('ROLE_USER')")
     */
    public function uploadAction(City $city, UserInterface $user = null): Response
    {
        return $this->render('CityHeaderImage/upload.html.twig', [
            'city' => $city,
        ]);
    }
}
