<?php declare(strict_types=1);

namespace App\Controller;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HelpController extends AbstractController
{
    const HELP_CATEGORY_ID = 3;
    const FAQ_CATEGORY_ID = 2;
    const ABOUT_CATEGORY_ID = 1;

    public function helpAction(): Response
    {
        $mainCategory = $this->getHelpCategoryRepository()->find(self::HELP_CATEGORY_ID);

        return $this->render('Help/two_columns.html.twig', [
            'mainCategory' => $mainCategory,
        ]);
    }

    public function faqAction(): Response
    {
        $mainCategory = $this->getHelpCategoryRepository()->find(self::FAQ_CATEGORY_ID);

        return $this->render('Help/one_column.html.twig', [
            'mainCategory' => $mainCategory,
        ]);
    }

    public function aboutAction(): Response
    {
        $mainCategory = $this->getHelpCategoryRepository()->find(self::ABOUT_CATEGORY_ID);

        return $this->render('Help/one_column.html.twig', [
            'mainCategory' => $mainCategory,
        ]);
    }
}
