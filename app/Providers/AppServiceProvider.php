<?php

namespace App\Providers;

use App\Models\Audit;
use App\Models\Observation;
use App\Observers\AuditObserver;
use App\Observers\ObservationObserver;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Audit::observe(AuditObserver::class);
        Observation::observe(ObservationObserver::class);
        CreateAction::configureUsing(function ($action) {
            return $action->slideOver();
        });
        EditAction::configureUsing(function ($action) {
            return $action->slideOver();
        });
    }
}
