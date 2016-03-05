<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Metadata;

class Metadata
{
    /**
     * @var String $author
     */
    protected $author = null;

    /**
     * @var \DateTime $date
     */
    protected $date = null;

    /**
     * @var String $description
     */
    protected $description = null;

    /**
     * @var String $title
     */
    protected $title = null;

    /**
     * @var String $keywords
     */
    protected $keywords = null;

    /**
     * @return String
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param String $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return String
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param String $description
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return String
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param String $title
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return String
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param String $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getAllMetaData()
    {
        $metaString = '';

        if ($this->author) {
            $metaString .= '<meta name="author" content="'.$this->author.'" />';
            $metaString .= "\n";
        }

        if ($this->date) {
            $metaString .= '<meta name="date" content="'.$this->date->format('Y-m-d H:i:s').'" />';
            $metaString .= "\n";
        }

        if ($this->title) {
            $metaString .= '<title>'.$this->title.'</title>';
            $metaString .= "\n";
        }

        if ($this->keywords) {
            $metaString .= '<meta name="keywords" content="'.$this->keywords.'" />';
            $metaString .= "\n";
        }

        if ($this->description) {
            $metaString .= '<meta name="description" content="'.$this->description.'" />';
            $metaString .= "\n";
        }

        return $metaString;
    }
}