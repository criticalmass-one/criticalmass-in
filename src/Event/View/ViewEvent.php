<?php declare(strict_types=1);

namespace App\Event\View;

use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use Symfony\Contracts\EventDispatcher\Event;

class ViewEvent extends Event
{
    final const NAME = 'viewable.view';

    public function __construct(protected ViewableEntity $viewable)
    {
    }

    public function getViewable(): ViewableEntity
    {
        return $this->viewable;
    }
}
