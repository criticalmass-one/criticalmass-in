<?php

namespace Criticalmass\Bundle\AppBundle\Manager\PostManager;

use Criticalmass\Bundle\AppBundle\Manager\AbstractManager;
use Criticalmass\Bundle\AppBundle\Repository\PostRepository;

/**
 * @deprecated
 */
class PostManager extends AbstractManager
{
    /** @var PostRepository $postRepository */
    protected $postRepository = null;

    public function __construct($doctrine)
    {
        parent::__construct($doctrine);

        $this->postRepository = $this->doctrine->getRepository('AppBundle:Post');
    }
}
