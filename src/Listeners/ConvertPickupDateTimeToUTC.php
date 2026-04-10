<?php

namespace twa\smsautils\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use twa\smsautils\Events\PickupRequestCreated;
use twa\smsautils\Models\Hub;

class ConvertPickupDateTimeToUTC
{
  
 

    /**
     * Handle the event.
     */
    public function handle(PickupRequestCreated $event): void
    {
        $pickupRequest = $event->pickupRequest;

        if (!$pickupRequest->pickup_date || !$pickupRequest->pickup_time_from || !$pickupRequest->pickup_time_to) {
            return;
        }

        $hub = $pickupRequest->hub;
      
        if (!$hub || !$hub->timezone) {
            return;
        }

        $pickupDates = filter_date_timezone_to_utc([ 
            'pickup_from' => $pickupRequest->pickup_date.' '.$pickupRequest->pickup_time_from, 
            'pickup_to' => $pickupRequest->pickup_date.' '.$pickupRequest->pickup_time_to 
        ] ,$hub->timezone);
        
        $pickupRequest->pickup_date_from = $pickupDates['pickup_from'];
        $pickupRequest->pickup_date_to =  $pickupDates['pickup_to'];
        $pickupRequest->save();
    }
}
