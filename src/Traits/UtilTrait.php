<?php declare(strict_types=1);

namespace App\Traits;

use Doctrine\ORM\EntityManagerInterface;
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

    /** @deprecated  */
    protected function getManager(): EntityManagerInterface
    {
        return $this->getDoctrine()->getManager();
    }
}
