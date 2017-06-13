<?php

namespace AppBundle\EntityInterface;

use AppBundle\Entity\User;

interface ArchiveableInterface
{
    public function setArchiveUser(User $archiveUser): ArchiveableInterface;
    public function getArchiveUser(): User;
    public function setArchiveParent(ArchiveableInterface $archiveParent): ArchiveableInterface;
    public function getArchiveParent(): ?ArchiveableInterface;
    public function setArchiveDateTime(\DateTime $archiveDateTime): ArchiveableInterface;
    public function getArchiveDateTime(): \DateTime;
    public function setIsArchived(bool $isArchived): ArchiveableInterface;
    public function getIsArchived(): bool;
    public function setArchiveMessage(string $archiveMessage): ArchiveableInterface;
    public function getArchiveMessage(): ?string;
    public function archive(User $user): ArchiveableInterface;
}
