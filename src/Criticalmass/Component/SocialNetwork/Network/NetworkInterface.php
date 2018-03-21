<?php

namespace Criticalmass\Component\SocialNetwork\Network;

interface NetworkInterface
{
    public function getName(): string;
    public function getIcon(): string;
    public function getBackgroundColor(): string;
    public function getTextColor(): string;

    public function getDetectorPriority(): int;
}
