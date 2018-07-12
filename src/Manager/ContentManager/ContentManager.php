<?php

namespace App\Manager\ContentManager;

use App\Entity\Content;
use App\Manager\AbstractManager;
use App\Manager\ContentManager\Exception\ContentNotFoundException;
use App\Repository\ContentRepository;

/**
 * @deprecated
 */
class ContentManager extends AbstractManager
{
    /** @var ContentRepository $contentRepository */
    protected $contentRepository = null;

    public function __construct($doctrine)
    {
        parent::__construct($doctrine);

        $this->contentRepository = $this->doctrine->getRepository('App:Content');
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
