<?php

namespace Spatie\MediaLibrary;

use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\Commands\CleanCommand;
use Spatie\MediaLibrary\Commands\ClearCommand;
use Laravel\Lumen\Application as LumenApplication;
use Spatie\MediaLibrary\Commands\RegenerateCommand;
use Illuminate\Foundation\Application as LaravelApplication;

class MediaLibraryServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        if ($this->app instanceof LaravelApplication) {
            $this->publishes([
                __DIR__.'/../config/laravel-medialibrary.php' => config_path('laravel-medialibrary.php'),
            ], 'config');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('laravel-medialibrary');
        }

        $mediaObserverClass = config('laravel-medialibrary.media_model_observer');
        $mediaClass = config('laravel-medialibrary.media_model');
        $mediaClass::observe(new $mediaObserverClass);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-medialibrary.php', 'laravel-medialibrary');

        $this->app->singleton(MediaRepository::class);

        $this->app->bind('command.medialibrary:regenerate', RegenerateCommand::class);
        $this->app->bind('command.medialibrary:clear', ClearCommand::class);
        $this->app->bind('command.medialibrary:clean', CleanCommand::class);

        $this->app->bind(FilesystemInterface::class, Filesystem::class);

        $this->commands([
            'command.medialibrary:regenerate',
            'command.medialibrary:clear',
            'command.medialibrary:clean',
        ]);
    }
}
