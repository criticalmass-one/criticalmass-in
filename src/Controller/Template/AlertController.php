<?php declare(strict_types=1);

namespace App\Controller\Template;

use App\Controller\AbstractController;
use App\Entity\Alert;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

class AlertController extends AbstractController
{
    public function showCurrentAlertsAction(ManagerRegistry $registry): Response
    {
        $alertList = $registry->getRepository(Alert::class)->findCurrentAlerts();

        return $this->render('Template/Includes/_alerts.html.twig', [
            'alertList' => $alertList,
        ]);
    }
}
