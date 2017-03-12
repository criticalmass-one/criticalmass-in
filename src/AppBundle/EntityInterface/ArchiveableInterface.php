<?php

namespace AppBundle\EntityInterface;

use AppBundle\Entity\User;

interface ArchiveableInterface
{
    public function setArchiveUser(User $archiveUser);
    public function getArchiveUser();
    public function setArchiveParent(ArchiveableInterface $archiveParent);
    public function getArchiveParent();
    public function setArchiveDateTime(\DateTime $archiveDateTime);
    public function getArchiveDateTime();
    public function setIsArchived(bool $isArchived);
    public function getIsArchived();
    public function setArchiveMessage($archiveMessage);
    public function getArchiveMessage();
    public function archive(User $user): ArchiveableInterface;
}