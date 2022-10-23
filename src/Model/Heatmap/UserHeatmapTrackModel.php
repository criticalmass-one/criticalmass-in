<?php declare(strict_types=1);

namespace App\Model\Heatmap;

use App\Entity\Heatmap;
use App\Entity\Track;
use App\Entity\User;

class UserHeatmapTrackModel
{
    public function __construct(protected User $user, protected Heatmap $heatmap, protected array $trackList = [])
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getHeatmap(): Heatmap
    {
        return $this->heatmap;
    }

    public function getTrackList(): array
    {
        return $this->trackList;
    }

    public function addTrack(Track $track): UserHeatmapTrackModel
    {
        $this->trackList[] = $track;

        return $this;
    }
}
