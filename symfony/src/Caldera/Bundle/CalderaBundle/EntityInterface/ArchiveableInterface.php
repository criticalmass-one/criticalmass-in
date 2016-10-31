<?php

namespace Caldera\Bundle\CalderaBundle\EntityInterface;

use Caldera\Bundle\CalderaBundle\Entity\User;

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
    public function setArchiveMessage(string $archiveMessage);
    public function getArchiveMessage();
}