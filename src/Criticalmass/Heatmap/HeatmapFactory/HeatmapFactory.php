<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\HeatmapFactory;

use App\Entity\City;
use App\Entity\Heatmap;
use App\Entity\Ride;
use App\Entity\User;

class HeatmapFactory implements HeatmapFactoryInterface
{
    /** @var Heatmap $heatmap */
    protected $heatmap;

    public function __construct()
    {
        $this->heatmap = new Heatmap();
    }

    public function withCity(City $city): HeatmapFactoryInterface
    {
        $this->heatmap->setCity($city);

        return $this;
    }

    public function withRide(Ride $ride): HeatmapFactoryInterface
    {
        $this->heatmap->setRide($ride);

        return $this;
    }

    public function setUser(User $user): HeatmapFactoryInterface
    {
        $this->heatmap->setUser($user);

        return $this;
    }

    public function build(): Heatmap
    {
        $this->heatmap->setIdentifier($this->generateIdentifier());

        return $this->heatmap;
    }

    protected function generateIdentifier(): string
    {
        if ($ride = $this->heatmap->getRide()) {
            return sprintf('%s-%s',
                $ride->getCity()->getMainSlug()->getSlug(),
                $ride->getSlug() ?? $ride->getDateTime()->format('Y-m-d')
            );
        }

        if ($city = $this->heatmap->getCity()) {
            return sprintf('%s',
                $city->getMainSlug()->getSlug()
            );
        }
    }
}
