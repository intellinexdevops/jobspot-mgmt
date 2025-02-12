<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

class DynamicRouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }


    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $controllerNamespace = 'App\\Http\\Controllers\\Api';

        foreach (glob(app_path('Http/Controllers/Api/*.php')) as $file) {
            $controllerClass = $controllerNamespace . '\\' . basename($file, '.php');
            $this->registerControllerRoutes($controllerClass);
        }
    }

    private function registerControllerRoutes($controllerClass)
    {
        try {
            $reflection = new ReflectionClass($controllerClass);

            $modelName = Str::snake(Str::replaceLast('Controller', '', $reflection->getShortName()));

            if (!$reflection->isAbstract() && $reflection->isSubclassOf('App\Http\Controllers\Controller')) {
                foreach ($reflection->getMethods() as $method) {
                    if ($method->isPublic() && $method->class === $controllerClass) {
                        // $endpoint = "/api/$modelName/" . Str::kebab($method->name);
                        $endpoint = "/api/$modelName/" . $method->name;

                        Route::post($endpoint, [$controllerClass, $method->name]);
                    }
                }
            }
        } catch (\Exception $e) {
            logger()->error("Failed to register routes for $controllerClass: " . $e->getMessage());
        }
    }
}
