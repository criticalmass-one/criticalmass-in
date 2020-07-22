<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Region;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CycleController extends BaseController
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a list of city cycles",
     *  section="Cycles",
     *  parameters={
     *     {"name"="citySlug", "dataType"="string", "required"=false, "description"="Provide a city slug"},
     *     {"name"="regionSlug", "dataType"="string", "required"=false, "description"="Provide a region slug"},
     *     {"name"="validFrom", "dataType"="date", "required"=false, "description"="Only retrieve cycles valid after the provied date"},
     *     {"name"="validUntil", "dataType"="date", "required"=false, "description"="Only retrieve cycles valid before the provied date"},
     *     {"name"="validNow", "dataType"="bool", "required"=false, "description"="Only retrieve cycles valid for the current month"},
     *     {"name"="dayOfWeek", "dataType"="int", "required"=false, "description"="Limit the results to this day of week"},
     *     {"name"="weekOfMonth", "dataType"="int", "required"=false, "description"="Limit the results to this week of month"},
     *  },
     * )
     *
     * @ParamConverter("city", class="App:City", isOptional=true)
     * @ParamConverter("region", class="App:Region", isOptional=true)
     * @ParamConverter("validFrom", class="DateTime", isOptional=true)
     * @ParamConverter("validUntil", class="DateTime", isOptional=true)
     */
    public function listAction(Request $request, ManagerRegistry $managerRegistry, City $city = null, Region $region = null, \DateTime $validFrom = null, \DateTime $validUntil = null): Response
    {
        $validNow = $request->query->getBoolean('validNow', null);
        $dayOfWeek = $request->query->getInt('dayOfWeek', null);
        $weekOfMonth = $request->query->getInt('weekOfMonth', null);

        $cycleList = $managerRegistry->getRepository(CityCycle::class)->findForApi($city, $region, $validFrom, $validUntil, $validNow, $dayOfWeek, $weekOfMonth);

        $context = new Context();

        $view = View::create();
        $view
            ->setContext($context)
            ->setData($cycleList)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }
}
