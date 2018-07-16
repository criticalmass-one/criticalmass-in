<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing\Network;

use App\Criticalmass\Sharing\ShareableInterface\Shareable;

class PinterestShareNetwork extends AbstractShareNetwork
{
    protected $name = 'Pinterest';

    protected $icon = 'fa-pinterest';

    protected $backgroundColor = 'rgb(189, 33, 37)';

    protected $textColor = 'white';

    protected $openSharewindow = true;

    public function createUrlForShareable(Shareable $shareable): string
    {
        $pinterestShareUrl = 'https://www.pinterest.com/pin/create/link?url=%s&description=%s';

        return sprintf($pinterestShareUrl, urlencode($this->getShareUrl($shareable)), urlencode($this->getShareTitle($shareable)));
    }
}
