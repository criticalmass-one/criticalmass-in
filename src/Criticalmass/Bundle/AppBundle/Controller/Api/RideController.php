<?php

namespace Criticalmass\Bundle\AppBundle\Controller\Api;

use Criticalmass\Bundle\AppBundle\Traits\RepositoryTrait;
use Criticalmass\Bundle\AppBundle\Traits\UtilTrait;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class RideController extends BaseController
{
    use RepositoryTrait;
    use UtilTrait;

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns ride details"
     * )
     */
    public function showAction(string $citySlug, string $rideDate): Response
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $view = View::create();
        $view
            ->setData($ride)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns details of the next ride in the city"
     * )
     */
    public function showCurrentAction(string $citySlug): Response
    {
        $city = $this->getCheckedCity($citySlug);

        $ride = $this->getRideRepository()->findCurrentRideForCity($city);

        $view = View::create();
        $view
            ->setData($ride)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Lists all next rides"
     * )
     */
    public function listAction(Request $request): Response
    {
        $region = null;
        $city = null;
        $dateTime = new \DateTime();
        $fullMonth = true;

        if ($request->query->get('region')) {
            $region = $this->getRegionRepository()->findOneBySlug($request->query->get('region'));

            if (!$region) {
                throw $this->createNotFoundException('Region not found');
            }
        }

        if ($request->query->get('city')) {
            $city = $this->getRegionRepository()->findOneBySlug($request->query->get('city'));

            if (!$city) {
                throw $this->createNotFoundException('Region not found');
            }
        }

        if ($request->query->get('year') && $request->query->get('month') && $request->query->get('day')) {
            $dateTime = new \DateTime(
                sprintf('%d-%d-%d',
                    $request->query->get('year'),
                    $request->query->get('month'),
                    $request->query->get('day')
                )
            );

            $fullMonth = false;
        } elseif ($request->query->get('year') && $request->query->get('month')) {
            $dateTime = new \DateTime(
                sprintf('%d-%d-01',
                    $request->query->get('year'),
                    $request->query->get('month')
                )
            );
        }

        $rideList = $this->getRideRepository()->findRides(
            $dateTime,
            $fullMonth,
            $city,
            $region
        );

        $context = new Context();
        $context
            ->addGroup('ride-list');

        $view = View::create();
        $view
            ->setData($rideList)
            ->setFormat('json')
            ->setStatusCode(200)
            ->setContext($context)
        ;

        return $this->handleView($view);
    }
}
