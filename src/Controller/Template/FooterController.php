<?php declare(strict_types=1);

namespace App\Controller\Template;

use App\Controller\AbstractController;
use App\Entity\Promotion;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;

class FooterController extends AbstractController
{
    public function promotionList(RegistryInterface $registry): Response
    {
        $promotionList = $registry->getRepository(Promotion::class)->findAll();

        return $this->render('Template/Includes/_footer_promotion_list.html.twig', [
            'promotionList' => $promotionList,
        ]);
    }
}
