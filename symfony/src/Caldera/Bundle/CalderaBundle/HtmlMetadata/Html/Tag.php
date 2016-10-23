<?php

namespace Caldera\Bundle\CalderaBundle\HtmlMetadata\Html;

class Tag
{
    protected $tag = '';
    protected $content = '';
    protected $attributes = [];

    public function __construct(string $tag)
    {
        $this->tag = $tag;
    }

    public function setContent(string $content): Tag
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function addAttribute(string $key, string $value): Tag
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function __toString()
    {
        $source = '<'.$this->tag;

        foreach ($this->attributes as $key => $value) {
            $source .= ' ' . $key . '="' . $value . '"';
        }

        if ($this->content) {
            $source = '>'.$this->content.'</'.$this->tag.'>';
        } else {
            $source = ' />';
        }

        return $source;
    }
}