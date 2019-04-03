<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use App\Traits\RepositoryTrait;
use App\Traits\UtilTrait;
use App\Criticalmass\Util\DateTimeUtil;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @ParamConverter("ride", class="App:Ride")
     */
    public function showAction(Ride $ride): Response
    {
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
     * @ParamConverter("city", class="App:City")
     */
    public function showCurrentAction(Request $request, City $city): Response
    {
        $ride = $this->getRideRepository()->findCurrentRideForCity($city, (bool) $request->get('cycleMandatory', false), (bool) $request->get('slugsAllowd', true));

        if (!$ride) {
            return new JsonResponse([], 200, []); // @todo this should return 404, but i have no clue how to handle multiple jquery requests then
        }

        $view = View::create();
        $view
            ->setData($ride)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }

    /**
     * Get a list of critical mass rides.
     *
     * This list may be limited to city or region by providing a city or region slug. You may also limit the list to a specific month or a specific day.
     *
     * If you do not provide <code>year</code> and <code>month</code>, results will be limited to the current month.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Lists rides",
     *  parameters={
     *     {"name"="region", "dataType"="string", "required"=false, "description"="Provide a region slug"},
     *     {"name"="city", "dataType"="string", "required"=false, "description"="Provide a city slug"},
     *     {"name"="year", "dataType"="string", "required"=false, "description"="Limit the result set to this year. If not set, we will search in the current month."},
     *     {"name"="month", "dataType"="string", "required"=false, "description"="Limit the result set to this year. Must be combined with 'year'. If not set, we will search in the current month."},
     *     {"name"="day", "dataType"="string", "required"=false, "description"="Limit the result set to this day."}
     *  }
     * )
     */
    public function listAction(Request $request): Response
    {
        $region = null;
        $city = null;
        $dateTime = new \DateTime();
        $fromDateTime = null;
        $untilDateTime = null;

        if ($request->query->get('region')) {
            $region = $this->getRegionRepository()->findOneBySlug($request->query->get('region'));

            if (!$region) {
                throw $this->createNotFoundException('Region not found');
            }
        }

        if ($request->query->get('city')) {
            /** @var CitySlug $citySlug */
            $citySlug = $this->getCitySlugRepository()->findOneBySlug($request->query->get('city'));

            if ($citySlug) {
                $city = $citySlug->getCity();
            }

            if (!$city) {
                throw $this->createNotFoundException('City not found');
            }
        }

        if ($request->query->get('year') && $request->query->get('month') && $request->query->get('day')) {
            try {
                $dateTime = new \DateTime(
                    sprintf('%d-%d-%d',
                        $request->query->get('year'),
                        $request->query->get('month'),
                        $request->query->get('day')
                    )
                );

                $fromDateTime = DateTimeUtil::getDayStartDateTime($dateTime);
                $untilDateTime = DateTimeUtil::getDayEndDateTime($dateTime);
            } catch (\Exception $e) {
                throw $this->createNotFoundException('Date not found');
            }
        } elseif ($request->query->get('year') && $request->query->get('month')) {
            try {
                $dateTime = new \DateTime(
                    sprintf('%d-%d-01',
                        $request->query->get('year'),
                        $request->query->get('month')
                    )
                );

                $fromDateTime = DateTimeUtil::getMonthStartDateTime($dateTime);
                $untilDateTime = DateTimeUtil::getMonthEndDateTime($dateTime);
            } catch (\Exception $e) {
                throw $this->createNotFoundException('Date not found');
            }
        } elseif ($request->query->get('year')) {
            try {
                $dateTime = new \DateTime(
                    sprintf('%d-01-01',
                        $request->query->get('year')
                    )
                );

                $fromDateTime = DateTimeUtil::getYearStartDateTime($dateTime);
                $untilDateTime = DateTimeUtil::getYearEndDateTime($dateTime);
            } catch (\Exception $e) {
                throw $this->createNotFoundException('Date not found');
            }
        }

        $rideList = $this->getRideRepository()->findRides(
            $fromDateTime,
            $untilDateTime,
            $city,
            $region
        );

        $context = new Context();
        $context->addGroup('ride-list');

        $view = View::create();
        $view
            ->setData($rideList)
            ->setFormat('json')
            ->setStatusCode(200)
            ->setContext($context);

        return $this->handleView($view);
    }
}
