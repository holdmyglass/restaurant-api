<?php

namespace Modules\Product\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Product\Interfaces\V1\PricableInterface;
use Modules\Product\Interfaces\V1\ReadPriceRepositoryInterface;
use Modules\Product\Interfaces\V1\ReadProductCategoryRepositoryInterface;
use Modules\Product\Interfaces\V1\ReadProductOptionItemRepositoryInterface;
use Modules\Product\Interfaces\V1\ReadProductOptionRepositoryInterface;
use Modules\Product\Interfaces\V1\ReadProductRepositoryInterface;
use Modules\Product\Interfaces\V1\WritePriceRepositoryInterface;
use Modules\Product\Interfaces\V1\WriteProductCategoryRepositoryInterface;
use Modules\Product\Interfaces\V1\WriteProductOptionItemRepositoryInterface;
use Modules\Product\Interfaces\V1\WriteProductOptionRepositoryInterface;
use Modules\Product\Interfaces\V1\WriteProductRepositoryInterface;
use Modules\Product\Models\Product;
use Modules\Product\Repositories\V1\PriceRepository;
use Modules\Product\Repositories\V1\ProductCategoryRepository;
use Modules\Product\Repositories\V1\ProductOptionItemRepository;
use Modules\Product\Repositories\V1\ProductOptionRepository;
use Modules\Product\Repositories\V1\ProductRepository;
use Nwidart\Modules\Traits\PathNamespace;

class ProductServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Product';

    protected string $nameLower = 'product';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->bindRepositories();
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([module_path($this->name, 'config/config.php') => config_path($this->nameLower.'.php')], 'config');
        $this->mergeConfigFrom(module_path($this->name, 'config/config.php'), $this->nameLower);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        $componentNamespace = $this->module_namespace($this->name, $this->app_path(config('modules.paths.generator.component-class.path')));
        Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }

    private function bindRepositories(): void
    {
        $this->app->bind(ReadProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(WriteProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ReadProductCategoryRepositoryInterface::class, ProductCategoryRepository::class);
        $this->app->bind(WriteProductCategoryRepositoryInterface::class, ProductCategoryRepository::class);
        $this->app->bind(ReadPriceRepositoryInterface::class, PriceRepository::class);
        $this->app->bind(WritePriceRepositoryInterface::class, PriceRepository::class);
        $this->app->bind(PricableInterface::class, Product::class);
        $this->app->bind(ReadProductOptionRepositoryInterface::class, ProductOptionRepository::class);
        $this->app->bind(WriteProductOptionRepositoryInterface::class, ProductOptionRepository::class);
        $this->app->bind(ReadProductOptionItemRepositoryInterface::class, ProductOptionItemRepository::class);
        $this->app->bind(WriteProductOptionItemRepositoryInterface::class, ProductOptionItemRepository::class);
    }
}
