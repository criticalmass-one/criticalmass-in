<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExploreController extends AbstractController
{
    #[Route('/explore', name: 'caldera_criticalmass_explore', priority: 200)]
    public function indexAction(): Response
    {
        return $this->render('Explore/index.html.twig');
    }
}
