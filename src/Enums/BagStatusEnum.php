<?php

namespace twa\smsautils\Enums;

enum BagStatusEnum: string
{
    case PENDING = 'pending';

    case COMPLETED = 'completed';
    case ASSIGNED_RUNSHEET = 'assigned_runsheet';
    case OUT_FOR_DELIVERY = 'out_for_delivery';
    case DELIVERED = 'delivered';
    case NOT_DELIVERED = 'not_delivered';
}