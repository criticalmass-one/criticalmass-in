<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing\Network;

use App\Criticalmass\Sharing\ShareableInterface\Shareable;

class WhatsappShareNetwork extends AbstractShareNetwork
{
    protected $name = 'WhatsApp';

    protected $icon = 'fa-whatsapp';

    protected $backgroundColor = 'rgb(37, 211, 102)';

    protected $textColor = 'white';

    protected $openSharewindow = false;

    public function createUrlForShareable(Shareable $shareable): string
    {
        $whatsappShareUrl = 'whatsapp://send?text=%s';

        $text = sprintf('%s%20%s', $this->getShareUrl($shareable), $this->getShareTitle($shareable));

        return sprintf($whatsappShareUrl, $text);
    }
}
