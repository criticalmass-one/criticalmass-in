<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\CityCycle;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
     */
    public function listAction(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $cycleList = $managerRegistry->getRepository(CityCycle::class)->findAll();

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
