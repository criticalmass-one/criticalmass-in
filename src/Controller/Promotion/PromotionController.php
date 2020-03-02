<?php declare(strict_types=1);

namespace App\Controller\Promotion;

use App\Entity\Promotion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PromotionController extends AbstractController
{
    /**
     * @ParamConverter("promotion", class="App:Promotion")
     */
    public function showAction(Promotion $promotion): Request
    {
        return $this->render('Promotion/index.html.twig');
    }
}
