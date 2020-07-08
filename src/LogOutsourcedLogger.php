<?php

namespace LogOutsourcedSdk;

use LogOutsourcedSdk\LogOutsourced;
use Psr\Log\AbstractLogger;

class LogOutsourcedLogger extends AbstractLogger
{
    private $logOutsourced;

    public function __construct(LogOutsourced $logOutsourced)
    {
        $this->logOutsourced = $logOutsourced;
    }

     public function log($level, $message, array $context = array())
     {
         $this->logOutsourced->single($level, $message, $context);
     }
}