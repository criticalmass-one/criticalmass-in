<?php

namespace Caldera\Bundle\CalderaBundle\Manager\ContentManager;

use Caldera\Bundle\CalderaBundle\Entity\Content;
use Caldera\Bundle\CalderaBundle\Manager\AbstractManager;
use Caldera\Bundle\CalderaBundle\Manager\ContentManager\Exception\ContentNotFoundException;
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
        /** @var Content $content */
        $content = $this->contentRepository->findBySlug($slug);

        if (!$content) {
            throw new ContentNotFoundException(sprintf('Could not find Content by slug: "%s"', $slug));
        }

        return $content;
    }
}