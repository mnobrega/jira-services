<?php
namespace App\Services\Statistics\Providers;

use View;
use Lang;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use Illuminate\Support\ServiceProvider;
use App\Services\Statistics\Providers\RouteServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;

class StatisticsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap migrations and factories for:
     * - `php artisan migrate` command.
     * - factory() helper.
     *
     * Previous usage:
     * php artisan migrate --path=src/Services/Statistics/database/migrations
     * Now:
     * php artisan migrate
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom([
            realpath(__DIR__ . '/../database/migrations')
        ]);

        $this->app->make(EloquentFactory::class)
            ->load(realpath(__DIR__ . '/../database/factories'));
    }

    /**
    * Register the Statistics service provider.
    *
    * @return void
    */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->registerResources();
    }

    /**
     * Register the Statistics service resource namespaces.
     *
     * @return void
     */
    protected function registerResources()
    {
        // Translation must be registered ahead of adding lang namespaces
        $this->app->register(TranslationServiceProvider::class);

        Lang::addNamespace('statistics', realpath(__DIR__.'/../resources/lang'));

        View::addNamespace('statistics', base_path('resources/views/vendor/statistics'));
        View::addNamespace('statistics', realpath(__DIR__.'/../resources/views'));
    }
}
