<?php

namespace Criticalmass\Bundle\AppBundle\Request\ParamConverter;

use Criticalmass\Bundle\AppBundle\Entity\Thread;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class ThreadParamConverter extends AbstractParamConverter
{
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $thread = null;

        $threadSlug = $request->get('threadSlug');

        if ($threadSlug) {
            $thread = $this->registry->getRepository(Thread::class)->findOneBySlug($threadSlug);
        }

        if ($thread) {
            $request->attributes->set($configuration->getName(), $thread);
        } else {
            $this->notFound($configuration);
        }
    }
}
