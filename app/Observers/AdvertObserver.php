<?php

namespace App\Observers;

use App\Models\Advert;
use App\Notifications\AdvertStatusChanged;

class AdvertObserver
{
    public function updated(Advert $advert): void
    {
        if ($advert->wasChanged('status') && in_array($advert->status, ['approved', 'rejected'])) {
            $advert->booking->user->notify(new AdvertStatusChanged($advert));
        }
    }
}
