<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing\Network;

use App\Criticalmass\Sharing\ShareableInterface\Shareable;

class TwitterShareNetwork extends AbstractShareNetwork
{
    protected $name = 'twitter';

    protected $icon = 'fa-twitter';

    protected $backgroundColor = 'rgb(85, 172, 238)';

    protected $textColor = 'white';

    protected $openSharewindow = true;

    public function createUrlForShareable(Shareable $shareable): string
    {
        $twitterShareUrl = 'https://twitter.com/share?url=%s&text=%s';

        return sprintf($twitterShareUrl, urlencode($this->getShareUrl($shareable)), urlencode($this->getShareTitle($shareable)));
    }
}
