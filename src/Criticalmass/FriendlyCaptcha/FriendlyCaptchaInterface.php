<?php declare(strict_types=1);

namespace App\Criticalmass\FriendlyCaptcha;

use Symfony\Component\HttpFoundation\Request;

interface FriendlyCaptchaInterface
{
    public function checkCaptcha(Request $request): bool;
}
