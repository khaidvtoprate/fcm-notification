<?php

namespace Thadico\FcmNotification\Providers;

use Illuminate\Support\ServiceProvider;
use Thadico\FcmNotification\Services\NotificationService;

class NotificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/notification.php', 'notification');
        
        $this->app->singleton('notification', function () {
            $modelClass = config('notification.user_model');

            return new NotificationService(is_object($modelClass) ? $modelClass : new $modelClass);
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Config/notification.php' => config_path('notification.php'),
        ], 'notification');
    }
}