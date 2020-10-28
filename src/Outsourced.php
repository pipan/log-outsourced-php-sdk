<?php

namespace OutsourcedSdk;

interface Outsourced
{
    public function logSingle($level, $message, $context = []);
    public function logBatch($logs);
    public function verifyPermissions($user, $permissions);
}