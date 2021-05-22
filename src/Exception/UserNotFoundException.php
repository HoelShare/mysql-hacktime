<?php
declare(strict_types=1);

namespace App\Exception;

use InvalidArgumentException;

class UserNotFoundException extends InvalidArgumentException
{
    public function __construct(private string $username)
    {
        parent::__construct(sprintf('Username (%s) not found!', $this->username));
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
