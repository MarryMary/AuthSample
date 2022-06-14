<?php

namespace Clsk\Elena\Exception\ExceptionThrower;

class ClearSkySiriusException extends \Exception
{
    protected $message;
    function __construct(String $message)
    {
        $this->message = $message;
    }
    function __toString()
    {
        return "CLSK QueryBuildingException:".$this->message;
    }
}