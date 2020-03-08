<?php

namespace GeeksAreForLife\Laravel\Artisan\Make;

use Illuminate\Support\ServiceProvider;

class DoctrineMakeProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/doctrine-make.php', 'doctrine-make'
        );
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/doctrine-make.php' => config_path('doctrine-make.php')
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                //MakeCommand::class,
                EntityMakeCommand::class,
                //MappingMakeCommand::class,
                //RepositoryInterfaceMakeCommand::class,
                //RepositoryImplementationMakeCommand::class,
                //FactoryMakeCommand::class,
            ]);
        }
    }
}