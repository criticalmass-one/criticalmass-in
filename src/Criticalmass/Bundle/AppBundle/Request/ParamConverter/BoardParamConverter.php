<?php

namespace Criticalmass\Bundle\AppBundle\Request\ParamConverter;

use Criticalmass\Bundle\AppBundle\Entity\Board;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class BoardParamConverter extends AbstractParamConverter
{
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $board = null;

        $boardSlug = $request->get('boardSlug');

        if ($boardSlug) {
            $board = $this->registry->getRepository(Board::class)->findOneBySlug($boardSlug);
        }

        if ($board) {
            $request->attributes->set($configuration->getName(), $board);
        } else {
            $this->notFound($configuration);
        }
    }
}
