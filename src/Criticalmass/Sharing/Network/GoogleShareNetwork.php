<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing\Network;

use App\Criticalmass\Sharing\ShareableInterface\Shareable;

class GoogleShareNetwork extends AbstractShareNetwork
{
    protected $name = 'Google+';

    protected $icon = 'fa-google-plus';

    protected $backgroundColor = 'rgb(220, 78, 65)';

    protected $textColor = 'white';

    protected $openSharewindow = true;

    public function createUrlForShareable(Shareable $shareable): string
    {
        $googleShareUrl = 'https://plus.google.com/share?url=%s';

        return sprintf($googleShareUrl, urlencode($this->getShareUrl($shareable)));
    }
}
