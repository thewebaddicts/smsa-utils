<?php

namespace twa\smsautils\Facades;

use twa\smsautils\Classes\AWB\AwbStatus;

use twa\smsautils\Models\ShipmentStatus;

class AwbStatusEnum
{
    public static function tryFrom($status)
    {
        return (new AwbStatus($status, null, null));
    }

    public static function from($status)
    {
        return (new AwbStatus($status, null, null));
    }

    public static function fromIdentifier($identifier)
    {
        return (new AwbStatus(null, $identifier, null));
    }

    public static function fromModel(ShipmentStatus $model)
    {
        return (new AwbStatus(null, null, $model));
    }
}
