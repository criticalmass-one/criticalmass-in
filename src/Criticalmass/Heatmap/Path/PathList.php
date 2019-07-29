<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Path;

class PathList
{
    /** @var array $list */
    protected $list = [];

    public function add(Path $path): PathList
    {
        $this->list[] = $path;

        return $this;
    }

    public function get(): ?Path
    {
        return array_shift($this->list);
    }
}