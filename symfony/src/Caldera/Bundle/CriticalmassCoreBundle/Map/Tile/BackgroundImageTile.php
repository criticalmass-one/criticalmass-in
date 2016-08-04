<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Map\Tile;

class BackgroundImageTile extends AbstractTile
{
    /** @var string $backgroundImageUrl */
    protected $backgroundImageUrl;

    /**
     * @return string
     */
    public function getBackgroundImageUrl()
    {
        return $this->backgroundImageUrl;
    }

    /**
     * @param string $backgroundImageUrl
     */
    public function setBackgroundImageUrl($backgroundImageUrl)
    {
        $this->backgroundImageUrl = $backgroundImageUrl;

        echo "LALLAA".$backgroundImageUrl."EFWEFWE";
        return $this;
    }
}