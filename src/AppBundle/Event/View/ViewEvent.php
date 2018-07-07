<?php declare(strict_types=1);

namespace AppBundle\Event\View;

use AppBundle\EntityInterface\ViewableInterface;
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
