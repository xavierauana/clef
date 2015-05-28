<?php namespace Xavierau\Clef;

    use Illuminate\Routing\Router;
    use Illuminate\Support\ServiceProvider;

    /**
 * Created by PhpStorm.
 * User: adrianexavier
 * Date: 27/5/15
 * Time: 6:59 PM
 */

class ClefServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        config([
            'config/clef.php',
        ]);
    }

    public function boot()
    {
        $this->setupRoutes($this->app->router);
        $this->publishes([
            __DIR__.'/config/clef.php' => config_path('clef.php'),
        ]);
    }

    private function setupRoutes(Router $router)
    {
        $router->group(['namespace' => 'Xavierau\Clef\Http\Controllers'], function($router)
        {
            require __DIR__.'/Http/routes.php';
        });
    }
}