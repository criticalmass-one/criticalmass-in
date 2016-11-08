<?php

namespace Caldera\Bundle\CalderaBundle\Manager\ContentManager;

use Caldera\Bundle\CalderaBundle\Entity\Content;
use Caldera\Bundle\CalderaBundle\Manager\AbstractManager;
use Caldera\Bundle\CalderaBundle\Manager\ContentManager\Exception\ContentNotFoundException;
use Caldera\Bundle\CalderaBundle\Repository\PostRepository;

class PostManager extends AbstractManager
{
    /** @var PostRepository $postRepository */
    protected $contentRepository = null;

    public function __construct($doctrine)
    {
        parent::__construct($doctrine);

        $this->postRepository = $this->doctrine->getRepository('CalderaBundle:Post');
    }

    public function getPostsForIncident()
    {
        
    }
}