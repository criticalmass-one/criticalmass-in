<?php declare(strict_types=1);

namespace App\Criticalmass\FriendlyCaptcha;

class Response
{
    private bool $success;
    private ?array $errors;

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): Response
    {
        $this->success = $success;

        return $this;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function setErrors(?array $errors): Response
    {
        $this->errors = $errors;

        return $this;
    }
}
