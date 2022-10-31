<?php declare(strict_types=1);

namespace App\Criticalmass\CriticalmassBlog\Model;

class BlogArticle
{
    public function __construct(protected string $title, protected string $permalink, protected \DateTime $dateTime)
    {
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
