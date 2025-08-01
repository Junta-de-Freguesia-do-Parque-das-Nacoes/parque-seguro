<?php

namespace App\Providers;

use App\Models\Accessory;
use App\Models\Asset;
use App\Models\Component;
use App\Models\Consumable;
use App\Models\License;
use App\Models\User;
use App\Models\Setting;
use App\Models\SnipeSCIMConfig;
use App\Observers\AccessoryObserver;
use App\Observers\AssetObserver;
use App\Observers\UserObserver;
use App\Observers\ComponentObserver;
use App\Observers\ConsumableObserver;
use App\Observers\LicenseObserver;
use App\Observers\SettingObserver;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\NotaEe;
use App\Observers\NotaEeObserver;

/**
 * This service provider handles setting the observers on models
 *
 * PHP version 5.5.9
 * @version    v3.0
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap application services.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v3.0]
     * @return void
     */
    

public function boot(UrlGenerator $url)
{
    /**
     * This is a workaround for proxies/reverse proxies that don't always pass the proper headers.
     *
     * Here, we check if the APP_URL starts with https://, which we should always honor,
     * regardless of how well the proxy or network is configured.
     *
     * We'll force the https scheme if the APP_URL starts with https://, or if APP_FORCE_TLS is set to true.
     *
     */
    if ((strpos(env('APP_URL'), 'https://') === 0) || (env('APP_FORCE_TLS'))) {
        $url->forceScheme('https');
    }

    // Prevents forging domain parts of URL via Host: headers unless explicitly allowed
    if (!env('APP_ALLOW_INSECURE_HOSTS')) {
        $url_parts = parse_url(config('app.url'));
        if ($url_parts && array_key_exists('scheme', $url_parts) && array_key_exists('host', $url_parts)) {
            URL::forceRootUrl(config('app.url'));
        } else {
            Log::error("Your APP_URL in your .env is misconfigured - it is: ".config('app.url').". Many things will work strangely unless you fix it.");
        }
    }

    // Adicionando a correção do morphMap
    Relation::morphMap([
        'asset' => 'App\Models\Asset',
        'user' => 'App\Models\User',
    ]);

    \Illuminate\Pagination\Paginator::useBootstrap();

    Schema::defaultStringLength(191);
    
    // Observadores
    Asset::observe(AssetObserver::class);
    User::observe(UserObserver::class);
    Accessory::observe(AccessoryObserver::class);
    Component::observe(ComponentObserver::class);
    Consumable::observe(ConsumableObserver::class);
    License::observe(LicenseObserver::class);
    Setting::observe(SettingObserver::class);
    NotaEe::observe(NotaEeObserver::class);

}


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Only load rollbar if there is a rollbar key and the app is in production
        if (($this->app->environment('production')) && (config('logging.channels.rollbar.access_token'))) {
            $this->app->register(\Rollbar\Laravel\RollbarServiceProvider::class);
        }

        $this->app->singleton('ArieTimmerman\Laravel\SCIMServer\SCIMConfig', SnipeSCIMConfig::class); // this overrides the default SCIM configuration with our own
    
    }
}
