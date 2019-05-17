<?php declare(strict_types=1);

namespace App\Controller\City;

use App\Controller\AbstractController;
use App\Criticalmass\SeoPage\SeoPageInterface;
use App\Factory\CityListFactory;
use Symfony\Component\HttpFoundation\Response;

class CityListController extends AbstractController
{
    public function listAction(SeoPageInterface $seoPage, CityListFactory $cityListFactory): Response
    {
        $seoPage->setDescription('Liste mit vielen weltweiten Critical-Mass-Radtouren.');

        return $this->render('CityList/list.html.twig', [
            'cityList' => $cityListFactory->getList(),
        ]);
    }
}
