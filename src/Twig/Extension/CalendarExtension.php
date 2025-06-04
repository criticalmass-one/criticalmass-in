<?php declare(strict_types=1);

namespace App\Twig\Extension;

use CalendR\Calendar;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Just an adapter for the old CalendRExtension.
 */
class CalendarExtension extends AbstractExtension
{
    public function __construct(protected Calendar $factory)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('calendr_year', [$this, 'getYear']),
            new TwigFunction('calendr_month', [$this, 'getMonth']),
            new TwigFunction('calendr_week', [$this, 'getWeek']),
            new TwigFunction('calendr_day', [$this, 'getDay']),
            new TwigFunction('calendr_events', [$this, 'getEvents']),
        ];
    }


    /**
     * @return mixed
     */
    public function getYear()
    {
        return call_user_func_array([$this->factory, 'getYear'], func_get_args());
    }

    /**
     * @return mixed
     */
    public function getMonth()
    {
        return call_user_func_array([$this->factory, 'getMonth'], func_get_args());
    }

    /**
     * @return mixed
     */
    public function getWeek()
    {
        return call_user_func_array([$this->factory, 'getWeek'], func_get_args());
    }

    /**
     * @return mixed
     */
    public function getDay()
    {
        return call_user_func_array([$this->factory, 'getDay'], func_get_args());
    }

    /**
     * @return mixed
     */
    public function getEvents()
    {
        return call_user_func_array([$this->factory, 'getEvents'], func_get_args());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::class;
    }
}
