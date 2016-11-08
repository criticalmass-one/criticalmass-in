<?php

namespace Caldera\Bundle\CalderaBundle\Manager\PostManager;

use Caldera\Bundle\CalderaBundle\Entity\Incident;
use Caldera\Bundle\CalderaBundle\Manager\AbstractManager;
use Caldera\Bundle\CalderaBundle\Repository\PostRepository;

class PostManager extends AbstractManager
{
    /** @var PostRepository $postRepository */
    protected $postRepository = null;

    public function __construct($doctrine)
    {
        parent::__construct($doctrine);

        $this->postRepository = $this->doctrine->getRepository('CalderaBundle:Post');
    }

    public function getPostsForIncident(Incident $incident): array
    {
        return $this->postRepository->getPostsForIncident($incident);
    }
}