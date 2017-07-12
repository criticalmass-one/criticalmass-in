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

    public function setTitle(string $title): SeoPage
    {
        $this->sonataSeoPage
            ->setTitle($title)
            ->addMeta('property', 'og:title', $title)
        ;

        return $this;
    }

    public function setDescription(string $description): SeoPage
    {
        $this->sonataSeoPage
            ->addMeta('name', 'description',$description)
            ->addMeta('property', 'og:description', $description)
        ;

        return $this;
    }
}