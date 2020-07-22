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
