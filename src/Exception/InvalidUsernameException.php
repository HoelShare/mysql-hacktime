<?php
declare(strict_types=1);

namespace App\Exception;

use InvalidArgumentException;

class InvalidUsernameException extends InvalidArgumentException
{
    public function __construct($message = '')
    {
        parent::__construct($message);
    }
}
