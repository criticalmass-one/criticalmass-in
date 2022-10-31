<?php declare(strict_types=1);

namespace App\Event\View;

use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use Symfony\Component\EventDispatcher\Event;

class ViewEvent extends Event
{
    const NAME = 'viewable.view';

    /** @var ViewableEntity $viewable */
    protected $viewable;

    public function __construct(ViewableEntity $viewable)
    {
        $this->viewable = $viewable;
    }

    public function getViewable(): ViewableEntity
    {
        return $this->viewable;
    }
}
