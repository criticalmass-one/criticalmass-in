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

        $html = '';

        foreach ($promotionList as $promotion) {
            echo 'lalala';
        }

        return new Response('ef');
    }
}