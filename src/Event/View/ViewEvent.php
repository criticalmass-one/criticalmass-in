<?php declare(strict_types=1);

namespace App\Event\View;

use App\EntityInterface\ViewableInterface;
use Symfony\Component\EventDispatcher\Event;

class ViewEvent extends Event
{
    const NAME = 'viewable.view';

    /** @var ViewableInterface $viewable */
    protected $viewable;

    public function __construct(ViewableInterface $viewable)
    {
        $this->viewable = $viewable;
    }

    public function getViewable(): ViewableInterface
    {
        return $this->viewable;
    }
}
