<?php declare(strict_types=1);

namespace App\Controller\CityCycle;

use App\Controller\AbstractController;
use App\Criticalmass\Cycles\Analyzer\CycleAnalyzerInterface;
use App\Entity\CityCycle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CityCycleAnalyzeController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("cityCycle", class="App:CityCycle")
     */
    public function analyzeAction(Request $request, CityCycle $cityCycle, CycleAnalyzerInterface $cycleAnalyzer): Response
    {
        $cycleAnalyzer->setCity($cityCycle->getCity())
            ->analyze();
        return new Response('fefef');
    }
}
