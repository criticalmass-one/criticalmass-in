<?php declare(strict_types=1);

namespace App\Request\ParamConverter;

use App\Entity\Region;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class RegionParamConverter extends AbstractParamConverter
{
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $regionSlug = $request->get('regionSlug');

        if ($regionSlug) {
            $region = $this->registry->getRepository(Region::class)->findOneBySlug($regionSlug);
        }

        if ($region) {
            $request->attributes->set($configuration->getName(), $region);
        } else {
            $this->notFound($configuration);
        }
    }
}
