<?php

namespace Jeffreyvr\SimpleMedia;

use Jeffreyvr\SimpleMedia\Media;
use Illuminate\Support\ServiceProvider;
use Jeffreyvr\SimpleMedia\MediaObserver;

class SimpleMediaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerPublishables();

        Media::observe(new MediaObserver);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/simple-media.php', 'simple-media');
    }

    protected function registerPublishables(): void
    {
        $this->publishes([
            __DIR__.'/../config/simple-media.php' => config_path('simple-media.php'),
        ], 'config');

        if (! class_exists('CreateMediaTable')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_media_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_media_table.php'),
            ], 'migrations');
        }

        if (! class_exists('CreateMediaRelationsTable')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_media_relations_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_media_relations_table.php'),
            ], 'migrations');
        }
    }
}