<?php
namespace Etlok\Crux;

use Etlok\Crux\Console\BuildResource;
use Etlok\Crux\Console\BuildController;
use Etlok\Crux\Console\BuildDefinition;
use Etlok\Crux\Console\BuildModel;
use Etlok\Crux\Console\BuildRequest;
use Etlok\Crux\Console\InstallCrux;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CruxServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->commands([
                InstallCrux::class,
                BuildDefinition::class,
                BuildController::class,
                BuildModel::class,
                BuildRequest::class,
                BuildResource::class
            ]);

            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('crux.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/crux'),
            ], 'views');

            $this->publishes([
                __DIR__.'/./Console/stubs' => base_path('stubs'),
            ], 'stubs');

        }
        Route::prefix(config('crux.web.prefix'))->middleware(config('crux.web.middleware'))->group(function() {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });

        Route::prefix(config('crux.api.prefix'))->middleware(config('crux.api.middleware'))->group(function() {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crux');


    }
}