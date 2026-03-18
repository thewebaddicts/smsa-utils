<?php

namespace twa\smsautils\Enums;

enum AddressTypeEnum: string
{
    case RESIDENTIAL = 'RESIDENTIAL';
    case COMMERCIAL = 'COMMERCIAL';
    case WAREHOUSE = 'WAREHOUSE';
    case BILLING = 'BILLING';
    case SHIPPING = 'SHIPPING';
} 