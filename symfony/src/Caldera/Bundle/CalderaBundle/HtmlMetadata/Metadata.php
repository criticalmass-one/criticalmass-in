<?php

namespace Caldera\Bundle\CalderaBundle\HtmlMetadata;

use Caldera\Bundle\CalderaBundle\HtmlMetadata\Html\Tag;

class Metadata
{
    /** @var array $tags */
    protected $tags = [];

    /**
     * @param String $author
     */
    public function setAuthor(string $author): Metadata
    {
        $tag = new Tag('meta');
        $tag
            ->addAttribute('name', 'author')
            ->addAttribute('content', $author);

        array_push($this->tags, $tag);

        return $this;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setDate(\DateTime $dateTime): Metadata
    {
        $tag = new Tag('meta');
        $tag
            ->addAttribute('name', 'date')
            ->addAttribute('content', $dateTime->format('Y-m-d H:i:s'));

        array_push($this->tags, $tag);

        return $this;
    }

    /**
     * @param String $description
     */
    public function setDescription(string $description): Metadata
    {
        $tag = new Tag('meta');
        $tag
            ->addAttribute('name', 'description')
            ->addAttribute('content', $description);

        array_push($this->tags, $tag);

        return $this;
    }

    /**
     * @param String $title
     */
    public function setTitle(string $title): Metadata
    {
        $tag = new Tag('title');
        $tag->setContent($title);

        array_push($this->tags, $tag);

        return $this;
    }

    /**
     * @param String $keywords
     */
    public function setKeywords(string $keywords): Metadata
    {
        $tag = new Tag('meta');
        $tag
            ->addAttribute('name', 'keywords')
            ->addAttribute('content', $keywords);

        array_push($this->tags, $tag);

        return $this;
    }

    public function getAllMetaData(): string
    {
        $metaString = '';

        foreach ($this->tags as $tag) {
            $metaString .= $tag;
        }

        return $metaString;
    }
}