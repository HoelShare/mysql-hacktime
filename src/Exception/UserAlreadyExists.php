<?php

namespace App\Exception;

class UserAlreadyExists extends InvalidUsernameException
{
    public function __construct(private string $username)
    {
        parent::__construct(sprintf('Username (%s) already exists', $username));
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}