<?php

namespace AppBundle\Request\ParamConverter;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class UserParamConverter extends AbstractParamConverter
{
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $user = null;

        $userId = $request->get('userId');

        if ($userId) {
            $user = $this->registry->getRepository(User::class)->find($userId);
        }

        if ($user) {
            $request->attributes->set($configuration->getName(), $user);
        } else {
            $this->notFound($configuration);
        }
    }
}
