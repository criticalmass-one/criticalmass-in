<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing\Network;

use App\Criticalmass\Sharing\ShareableInterface\Shareable;

class RedditShareNetwork extends AbstractShareNetwork
{
    protected $name = 'Reddit';

    protected $icon = 'fa-reddit';

    protected $backgroundColor = 'rgb(255, 69, 1)';

    protected $textColor = 'white';

    protected $openSharewindow = true;

    public function createUrlForShareable(Shareable $shareable): string
    {
        $redditShareUrl = 'https://ssl.reddit.com/submit?url=%s';

        return sprintf($redditShareUrl, urlencode($this->getShareUrl($shareable)));
    }
}
