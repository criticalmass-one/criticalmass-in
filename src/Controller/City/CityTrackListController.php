<?php declare(strict_types=1);

namespace App\Controller\City;

use App\Controller\AbstractController;
use App\Entity\City;
use App\Entity\Track;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CityTrackListController extends AbstractController
{
    /**
     * @ParamConverter("city", class="App:City")
     */
    public function listTracksAction(Request $request, City $city, RegistryInterface $registry, PaginatorInterface $paginator): Response
    {
        $query = $registry->getRepository(Track::class)->findByCityQuery($city, 'DESC');

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('City/track_list.html.twig', [
            'city' => $city,
            'pagination' => $pagination,
        ]);
    }
}
