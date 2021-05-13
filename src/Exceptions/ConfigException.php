<?php

namespace Smbear\Paypal\Exceptions;

use Throwable;

class ConfigException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}