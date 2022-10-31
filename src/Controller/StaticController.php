<?php declare(strict_types=1);

namespace App\Controller;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StaticController extends AbstractController
{
    public function displayStaticContentAction(string $slug): Response
    {
        $templateName = sprintf('Static/%s.html.twig', $slug);

        try {
            return $this->render($templateName);
        } catch (InvalidArgumentException $e) {
            throw $this->createNotFoundException(sprintf('There is no content for slug "%s"', $slug));
        }
    }
}
