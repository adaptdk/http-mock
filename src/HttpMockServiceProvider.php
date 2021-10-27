<?php

namespace Adaptdk\HttpMock;

use Adaptdk\HttpMock\Contracts\ContentTransformer;
use Adaptdk\HttpMock\Contracts\FilenameTransformer;
use Adaptdk\HttpMock\HttpMock;
use Adaptdk\HttpMock\Transformers\ContentTransformer as TransformersContentTransformer;
use Adaptdk\HttpMock\Transformers\FilenameTransformer as TransformersFilenameTransformer;
use Illuminate\Support\ServiceProvider;

class HttpMockServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('http-mock.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php',
            'http-mock'
        );

        $this->app->bind(ContentTransformer::class, TransformersContentTransformer::class);
        $this->app->bind(FilenameTransformer::class, TransformersFilenameTransformer::class);

        HttpMock::register();
    }
}
