<?php declare(strict_types=1);

namespace App\Request\ParamConverter;

use App\Entity\SocialNetworkFeedItem;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class SocialNetworkFeedItemParamConverter extends AbstractCriticalmassParamConverter
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry);
    }

    public function apply(Request $request, ParamConverter $configuration): void
    {
        $feedItemId = $request->get('feedItemId');

        $feedItem = null;

        if ($feedItemId) {
            $feedItem = $this->registry->getRepository(SocialNetworkFeedItem::class)->find($feedItemId);
        }

        if ($feedItem) {
            $request->attributes->set($configuration->getName(), $feedItem);
        } else {
            $this->notFound($configuration);
        }
    }
}
