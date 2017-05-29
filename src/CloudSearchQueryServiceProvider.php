<?php

namespace TheLHC\CloudSearchQuery;

use Illuminate\Support\ServiceProvider;

class CloudSearchQueryServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/cloud_search_query.php' => config_path('cloud_search_query.php')
        ], 'config');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/cloud_search_query.php', 'cloud_search_query');

        $this->app->bind(CloudSearchQuery::class, function ($app) {
            return new CloudSearchQuery(
                $app['config']->get('cloud_search_query')
            );
        });
    }

}
