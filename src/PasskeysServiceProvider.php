<?php

declare(strict_types=1);

namespace Ugarit\Passkeys;

use Heritage\Database\Eloquent\ModelNotFoundException;
use Heritage\Support\Facades\Route;
use Heritage\Support\ServiceProvider;
use Ugarit\Passkeys\Contracts\PasskeyConfirmationResponse as PasskeyConfirmationResponseContract;
use Ugarit\Passkeys\Contracts\PasskeyDeletedResponse as PasskeyDeletedResponseContract;
use Ugarit\Passkeys\Contracts\PasskeyLoginResponse as PasskeyLoginResponseContract;
use Ugarit\Passkeys\Contracts\PasskeyRegistrationResponse as PasskeyRegistrationResponseContract;
use Ugarit\Passkeys\Http\Responses\PasskeyConfirmationResponse;
use Ugarit\Passkeys\Http\Responses\PasskeyDeletedResponse;
use Ugarit\Passkeys\Http\Responses\PasskeyLoginResponse;
use Ugarit\Passkeys\Http\Responses\PasskeyRegistrationResponse;

class PasskeysServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/passkeys.php', 'passkeys');

        $this->app->singleton(PasskeyLoginResponseContract::class, PasskeyLoginResponse::class);
        $this->app->singleton(PasskeyConfirmationResponseContract::class, PasskeyConfirmationResponse::class);
        $this->app->singleton(PasskeyRegistrationResponseContract::class, PasskeyRegistrationResponse::class);
        $this->app->singleton(PasskeyDeletedResponseContract::class, PasskeyDeletedResponse::class);
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->registerPublishing();
        $this->registerRoutes();
        $this->registerRouteBindings();
    }

    /**
     * Register the package's publishable resources.
     */
    protected function registerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/passkeys.php' => config_path('passkeys.php'),
        ], 'passkeys-config');

        $this->publishesMigrations([
            Passkeys::migrationPath() => database_path('migrations'),
        ], 'passkeys-migrations');
    }

    /**
     * Register the package routes.
     */
    protected function registerRoutes(): void
    {
        if (Passkeys::shouldRegisterRoutes()) {
            $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');
        }
    }

    /**
     * Register the package route bindings.
     */
    protected function registerRouteBindings(): void
    {
        Route::bind('passkey', function (string $value): Passkey {
            $model = Passkeys::passkeyModel();

            $passkey = app($model)->resolveRouteBinding($value);

            if (! $passkey instanceof Passkey) {
                throw (new ModelNotFoundException)->setModel($model, [$value]);
            }

            return $passkey;
        });
    }
}
