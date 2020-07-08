<?php

namespace LogOutsourcedSdk;

interface LogOutsourced
{
    public function single($level, $message, $context = []);
    public function batch($logs);
}