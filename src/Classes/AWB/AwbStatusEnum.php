<?php

namespace twa\smsautils\Classes\AWB;



class AwbStatusEnum
{
    public static function tryFrom($status)
    {
        return (new AwbStatus($status));
    }

    public static function from($status)
    {
        return (new AwbStatus($status));
    }
}
