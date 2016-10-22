<?php

namespace Caldera\Bundle\CalderaBundle\Manager\ContentManager;

use Caldera\Bundle\CalderaBundle\Entity\Content;
use Caldera\Bundle\CalderaBundle\Manager\AbstractManager;
use Caldera\Bundle\CalderaBundle\Repository\ContentRepository;

class ContentManager extends AbstractManager
{
    /** @var ContentRepository $contentRepository */
    protected $contentRepository = null;

    public function __construct($doctrine)
    {
        parent::__construct($doctrine);

        $this->contentRepository = $this->doctrine->getRepository('CalderaBundle:Content');
    }

    public function getBySlug(string $slug): Content
    {
        return $this->contentRepository->findBySlug($slug);
    }
}