<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class RedirectingController extends AbstractController
{
    public function removeTrailingSlashAction(Request $request): RedirectResponse
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        return $this->redirect($url, RedirectResponse::HTTP_MOVED_PERMANENTLY);
    }
}
