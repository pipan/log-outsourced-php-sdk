<?php

namespace OutsourcedSdk;

use OutsourcedSdk\Outsourced;
use Psr\Log\AbstractLogger;

class OutsourcedLogger extends AbstractLogger
{
    private $outsourced;

    public function __construct(Outsourced $outsourced)
    {
        $this->outsourced = $outsourced;
    }

     public function log($level, $message, array $context = array())
     {
         $this->outsourced->logSingle($level, $message, $context);
     }
}