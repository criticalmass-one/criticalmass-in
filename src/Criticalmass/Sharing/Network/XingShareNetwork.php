<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing\Network;

use App\Criticalmass\Sharing\ShareableInterface\Shareable;

class XingShareNetwork extends AbstractShareNetwork
{
    protected $name = 'XING';

    protected $icon = 'fa-xing';

    protected $backgroundColor = 'rgb(1, 101, 104)';

    protected $textColor = 'white';

    protected $openSharewindow = true;

    public function createUrlForShareable(Shareable $shareable): string
    {
        $xingShareUrl = 'https://www.xing.com/social_plugins/share?&url=%s';

        return sprintf($xingShareUrl, urlencode($this->getShareUrl($shareable)));
    }
}
