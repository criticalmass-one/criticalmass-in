<?php declare(strict_types=1);

namespace App\Request\ParamConverter;

use App\Entity\SocialNetworkProfile;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class SocialNetworkProfileParamConverter extends AbstractCriticalmassParamConverter
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    public function apply(Request $request, ParamConverter $configuration): void
    {
        $profileId = $request->get('profileId');

        $profile = null;

        if ($profileId) {
            $profile = $this->registry->getRepository(SocialNetworkProfile::class)->find($profileId);
        }

        if ($profile) {
            $request->attributes->set($configuration->getName(), $profile);
        } else {
            $this->notFound($configuration);
        }
    }
}
