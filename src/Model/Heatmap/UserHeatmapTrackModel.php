<?php declare(strict_types=1);

namespace App\Model\Heatmap;

use App\Entity\Heatmap;
use App\Entity\Track;
use App\Entity\User;

class UserHeatmapTrackModel
{
    /** @var User $user */
    protected $user;

    /** @var Heatmap $heatmap */
    protected $heatmap;

    /** @var array $trackList */
    protected $trackList;

    public function __construct(User $user, Heatmap $heatmap, array $trackList = [])
    {
        $this->user = $user;
        $this->heatmap = $heatmap;
        $this->trackList = $trackList;
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
