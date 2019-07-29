<?php declare(strict_types=1);

namespace App\Controller\Heatmap;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HeatmapController extends AbstractController
{
    public function fooAction(): Response
    {
        return new Response('foo');
    }
}
