<?php

namespace Criticalmass\Bundle\AppBundle\Manager\ContentManager;

use Criticalmass\Bundle\AppBundle\Entity\Content;
use Criticalmass\Bundle\AppBundle\Manager\AbstractManager;
use Criticalmass\Bundle\AppBundle\Manager\ContentManager\Exception\ContentNotFoundException;
use Criticalmass\Bundle\AppBundle\Repository\ContentRepository;

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

        $this->contentRepository = $this->doctrine->getRepository('AppBundle:Content');
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
