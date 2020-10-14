<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing\Network;

use App\Criticalmass\Sharing\ShareableInterface\Shareable;

class MailShareNetwork extends AbstractShareNetwork
{
    protected $name = 'E-Mail';

    protected $icon = 'fa-envelope-o';

    protected $backgroundColor = 'white';

    protected $textColor = 'black';

    protected $openSharewindow = false;

    public function createUrlForShareable(Shareable $shareable): string
    {
        $mailShareUrl = 'mailto:?subject=%s&body=%s';

        $body = sprintf('%s: %s', $this->getShareIntro($shareable), $this->getShareUrl($shareable));

        return sprintf($mailShareUrl, urlencode($this->getShareTitle($shareable)), $body);
    }
}
