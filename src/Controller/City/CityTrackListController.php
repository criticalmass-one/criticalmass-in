<?php declare(strict_types=1);

namespace App\Controller\City;

use App\Controller\AbstractController;
use App\Entity\City;
use App\Entity\Track;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CityTrackListController extends AbstractController
{
    #[Route('/{citySlug}/tracks', name: 'caldera_criticalmass_city_listtracks', priority: 100)]
    public function listTracksAction(Request $request, City $city, ManagerRegistry $registry, PaginatorInterface $paginator): Response
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
