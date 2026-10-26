<?php declare(strict_types=1);

namespace App\EntityInterface;

use App\Entity\Thread;

interface BoardInterface
{
    public function getTitle(): ?string;

    public function setTitle(string $title): BoardInterface;

    public function getThreadNumber(): ?int;

    public function setThreadNumber(int $threadNumber): BoardInterface;

    public function incThreadNumber(): BoardInterface;

    public function decThreadNumber(): BoardInterface;

    public function getPostNumber();

    public function setPostNumber(int $postNumber): BoardInterface;

    public function incPostNumber(int $amount = 1): BoardInterface;

    public function decPostNumber(int $amount = 1): BoardInterface;

    public function getLastThread(): ?Thread;

    public function setLastThread(?Thread $thread): BoardInterface;
}
