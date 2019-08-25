<?php declare(strict_types=1);

namespace App\Controller\City;

use App\Controller\AbstractController;
use App\Entity\City;
use App\Entity\Track;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;

class CityTrackListController extends AbstractController
{
    /**
     * @ParamConverter("city", class="App:City")
     */
    public function listTracksAction(City $city, RegistryInterface $registry): Response
    {
        $trackList = $registry->getRepository(Track::class)->findByCity($city);

        return $this->render('City/track_list.html.twig', [
            'city' => $city,
            'trackList' => $trackList,
        ]);
    }
}
