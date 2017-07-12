<?php

namespace AppBundle\Seo;

use Sonata\SeoBundle\Seo\SeoPageInterface;

class SeoPage
{
    /** @var SeoPageInterface */
    protected $sonataSeoPage;

    public function __construct(SeoPageInterface $sonataSeoPage)
    {
        $this->sonataSeoPage = $sonataSeoPage;
    }
}