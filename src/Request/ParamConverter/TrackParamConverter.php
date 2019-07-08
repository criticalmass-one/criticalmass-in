<?php declare(strict_types=1);

namespace App\Request\ParamConverter;

use App\Entity\Track;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class TrackParamConverter extends AbstractCriticalmassParamConverter
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry);
    }

    public function apply(Request $request, ParamConverter $configuration): void
    {
        $trackId = $request->get('trackId');
        $track = null;

        if ($trackId) {
            $track = $this->registry->getRepository(Track::class)->find($trackId);
        }

        if ($track) {
            $request->attributes->set($configuration->getName(), $track);
        } else {
            $this->notFound($configuration);
        }
    }
}
