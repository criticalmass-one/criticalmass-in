<?php

namespace AppBundle\Controller;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HelpController extends AbstractController
{
    const HELP_CATEGORY_ID = 7;

    public function helpAction(Request $request): Response
    {
        $mainCategory = $this->getHelpCategoryRepository()->find(self::HELP_CATEGORY_ID);

        return $this->render('AppBundle:Help:help.html.twig', [
                'mainCategory' => $mainCategory,
            ]);
    }
}
