<?php

namespace App\Observers;

use App\Models\Audit;

class AuditObserver
{
    /**
     * Handle the Audit "created" event.
     */
    public function created(Audit $audit): void
    {
        // $audit->statuses()->attach(1);
    }

    /**
     * Handle the Audit "updated" event.
     */
    public function updated(Audit $audit): void
    {
        //
    }

    /**
     * Handle the Audit "deleted" event.
     */
    public function deleted(Audit $audit): void
    {
        //
    }

    /**
     * Handle the Audit "restored" event.
     */
    public function restored(Audit $audit): void
    {
        //
    }

    /**
     * Handle the Audit "force deleted" event.
     */
    public function forceDeleted(Audit $audit): void
    {
        //
    }
}
