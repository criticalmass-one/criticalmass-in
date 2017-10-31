<?php

namespace AppBundle\Controller;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HelpController extends AbstractController
{
    const HELP_CATEGORY_ID = 7;
    const FAQ_CATEGORY_ID = 8;

    public function helpAction(Request $request): Response
    {
        $mainCategory = $this->getHelpCategoryRepository()->find(self::HELP_CATEGORY_ID);

        return $this->render('AppBundle:Help:help.html.twig', [
            'mainCategory' => $mainCategory,
        ]);
    }

    public function faqAction(Request $request): Response
    {
        $mainCategory = $this->getHelpCategoryRepository()->find(self::FAQ_CATEGORY_ID);

        return $this->render('AppBundle:Help:faq.html.twig', [
            'mainCategory' => $mainCategory,
        ]);
    }
}
