<?php

namespace twa\smsautils\Enums;

enum ShipmentTypeEnum: string
{
    case PARCEL = 'PARCEL';
    case DOCUMENT = 'DOC';
   

    public function getDescription(): string
    {
         return match($this) {
            self::PARCEL => 'Standard parcels and packages',
            self::DOCUMENT => 'Small flat packages, documents, letters',
        };
    }

   public function getName(): string
    {
        return match($this) {
            self::PARCEL => 'Parcel',
            self::DOCUMENT => 'Document',
        };
    }

  public static function getOptions(): array
    {
        return collect(self::cases())->map(function ($case) {
            return [
                'value' => $case->value,
                'label' => $case->getName(),
                'description' => $case->getDescription(),
            ];
        })->toArray();
    }
} 