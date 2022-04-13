<?php

class ErrorResponse
{
    function __construct($message, $code) {
        $this->message = $message;
        $this->code = $code;
    }

    public $message;
    public $code;
}