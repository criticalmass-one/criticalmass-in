<?php declare(strict_types=1);

namespace App\Traits;

use Symfony\Component\HttpFoundation\Session\Session;

/** @deprecated */
trait UtilTrait
{
    /** @deprecated  */
    protected function getSession(): Session
    {
        $session = new Session();

        return $session;
    }
}
