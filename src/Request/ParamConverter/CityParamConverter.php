<?php declare(strict_types=1);

namespace App\Request\ParamConverter;

use App\Entity\City;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class CityParamConverter extends AbstractCriticalmassParamConverter
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry);
    }

    public function apply(Request $request, ParamConverter $configuration): void
    {
        $city = $this->findCityById($request);

        if (!$city) {
            $city = $this->findCityBySlug($request);;
        }

        if ($city) {
            $request->attributes->set($configuration->getName(), $city);
        } else {
            $this->notFound($configuration);
        }
    }

    protected function findCityById(Request $request): ?City
    {
        $cityId = $request->get('cityId');

        if ($cityId) {
            return $this->registry->getRepository(City::class)->find($cityId);
        }

        return null;
    }
}
