<?php

namespace twa\smsautils\Facades;

use twa\smsautils\Classes\AWB\AwbStatus;

class AwbStatusEnum
{
    public static function tryFrom($status)
    {
        return (new AwbStatus($status, null));
    }

    public static function from($status)
    {
        return (new AwbStatus($status, null));
    }

    public static function fromIdentifier($identifier)
    {
        return (new AwbStatus(null, $identifier));
    }
}
