<?php

namespace LoyalmeCRM\LoyalmePhpSdk\Exceptions;

use Exception;
use Throwable;

class LoyalmePhpSdk extends Exception
{
    protected $_errorData = [];

    public function __construct(string $message = "", int $code = 0, $errorData = [], Throwable $previous = null)
    {
        $this->_errorData = $errorData;
        parent::__construct($message, $code, $previous);
    }

    public function getErrorData()
    {
        return $this->_errorData;
    }
}
