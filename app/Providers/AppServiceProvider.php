<?php

namespace App\Providers;

use App\Models\Audit;
use App\Models\Observation;
use App\Observers\AuditObserver;
use App\Observers\ObservationObserver;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Illuminate\Support\Facades\URL;
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
        if (env('APP_ENV') !== 'local') {
            $this->app['request']->server->set('HTTPS', 'on');
            URL::forceScheme('https');
        }

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
