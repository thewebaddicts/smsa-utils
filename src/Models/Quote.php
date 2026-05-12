<?php

namespace twa\smsautils\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    protected $fillable = [
        'country_from',
        'country_to',
        'shipping_fees_amount',
        'shipping_fees_payment_type',
        'currency',
        'vat_amount',
        'total_amount',
        'product_code',
        'account_number',
        'created_at',
        'updated_at',
        'deleted_at',
        'shipment_type',
        'weight',
        'product',
        'vat_percentage',
        'promo_code',
        'cashier_discount_amount',
        'promo_code_discount',
        'total_discount_amount',
        'additional_charges',
        'cashier_id',
        'service_type_reference',
    ];

    public function paymentLines(): HasMany
    {
        return $this->hasMany(QuotePaymentLine::class, 'quote_id');
    }
}
