<?php declare(strict_types=1);

namespace App\Criticalmass\CriticalmassBlog\Model;

class BlogArticle
{
    protected string $title;
    protected string $permalink;
    protected \DateTime $dateTime;

    public function __construct(string $title, string $permalink, \DateTime $dateTime)
    {
        $this->title = $title;
        $this->permalink = $permalink;
        $this->dateTime = $dateTime;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPermalink(): string
    {
        return $this->permalink;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }
}
