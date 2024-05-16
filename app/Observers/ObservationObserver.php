<?php

namespace App\Observers;

use App\Models\Observation;

class ObservationObserver
{
    public function created(Observation $observation): void
    {
        $observation->status = 'draft';
    }
}
