<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Sharing\Network;

use AppBundle\Criticalmass\Sharing\ShareableInterface\Shareable;

class LinkedInShareNetwork extends AbstractShareNetwork
{
    protected $name = 'LinkedIn';

    protected $icon = 'fa-linkedin';

    protected $backgroundColor = 'rgb(0, 122, 182)';

    protected $textColor = 'white';

    protected $openSharewindow = true;

    public function createUrlForShareable(Shareable $shareable): string
    {
        $linkedinShareUrl = 'https://www.linkedin.com/cws/share?&url=%s';

        return sprintf($linkedinShareUrl, urlencode($this->getShareUrl($shareable)));
    }
}
