<?php

namespace FomBuilder\DynamicForm;

use FomBuilder\DynamicForm\Services\DynamicFormRenderer;
use FomBuilder\DynamicForm\Services\DynamicFormAutomationService;
use FomBuilder\DynamicForm\Services\DynamicFormSubmissionActivityService;
use FomBuilder\DynamicForm\Services\DynamicFormSubmissionService;
use FomBuilder\DynamicForm\Services\DynamicFormWorkflowService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class DynamicFormServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/dynamic-form.php', 'dynamic-form');

        $this->app->singleton(DynamicFormRenderer::class);
        $this->app->singleton(DynamicFormSubmissionActivityService::class);
        $this->app->singleton(DynamicFormAutomationService::class);
        $this->app->singleton(DynamicFormWorkflowService::class);
        $this->app->singleton(DynamicFormSubmissionService::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'dynamic-form');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Blade::directive('dynamicForm', function (string $expression): string {
            return "<?php try { echo app('".DynamicFormRenderer::class."')->render({$expression}); } catch (\\Throwable \$e) { report(\$e); } ?>";
        });

        $this->publishes([
            __DIR__.'/../config/dynamic-form.php' => config_path('dynamic-form.php'),
        ], 'dynamic-form-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/dynamic-form'),
        ], 'dynamic-form-views');
    }
}
