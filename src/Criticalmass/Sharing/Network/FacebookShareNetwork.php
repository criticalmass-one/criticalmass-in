<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing\Network;

use App\Criticalmass\Sharing\ShareableInterface\Shareable;

class FacebookShareNetwork extends AbstractShareNetwork
{
    protected $name = 'facebook';

    protected $icon = 'fa-facebook';

    protected $backgroundColor = 'rgb(59, 90, 153)';

    protected $textColor = 'white';

    protected $openSharewindow = true;

    public function createUrlForShareable(Shareable $shareable): string
    {
        $facebookShareUrl = 'https://www.facebook.com/sharer.php?u=%s&t=%s';

        return sprintf($facebookShareUrl, urlencode($this->getShareUrl($shareable)), urlencode($this->getShareTitle($shareable)));
    }
}
