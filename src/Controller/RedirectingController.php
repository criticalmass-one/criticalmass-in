<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class RedirectingController extends AbstractController
{
    #[Route(
        '/{url}',
        name: 'remove_trailing_slash',
        requirements: ['url' => '.*/$'],
        methods: ['GET'],
        priority: -255
    )]
    public function removeTrailingSlashAction(Request $request): RedirectResponse
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        if (str_starts_with($url, '//') || str_contains($url, '://')) {
            $url = '/';
        }

        return $this->redirect($url, RedirectResponse::HTTP_MOVED_PERMANENTLY);
    }
}
