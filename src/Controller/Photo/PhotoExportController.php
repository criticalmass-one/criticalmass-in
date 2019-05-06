<?php declare(strict_types=1);

namespace App\Controller\Photo;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Flagception\Bundle\FlagceptionBundle\Annotations\Feature;

/**
 * @Feature("photos")
 */
class PhotoExportController extends AbstractController
{
    public function listAction(): Response
    {
        return new Response('foo');
    }
}
