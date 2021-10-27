# Mock HTTP data

## Usage

### Transformers

You can overwrite the transformers to suit your own needs. For instance, you might want to use a different filename for the recorded requests. To do that, simply create your own transformer and overwrite it in the service container.

Example: NetcompanyFilenameTransformer

```php
<?php

namespace App\Transformers;

use Illuminate\Http\Client\Request;
use Adaptdk\HttpMock\Transformers\FilenameTransformer;

class NetcompanyFilenameTransformer extends FilenameTransformer
{
    /**
     * @param \Illuminate\Http\Client\Request $request
     * @return string
     */
    public function transform(Request $request): string
    {
        return 'my_custom_file_name.json';
    }
}
```

Then overwrite it in AppServiceProvider.php:

```php
<?php

namespace App\Providers;

use Adaptdk\HttpMock\Contracts\FilenameTransformer;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(FilenameTransformer::class, NetcompanyFilenameTransformer::class);
    }
}
```

### Middleware

When you perform requests in your tests, you can add headers to manipulate date and add a custom string to vary the mock data filenames, thus avoiding overlapping mock files.

To set a specific date as the base date of the tests, add the following middleware to your middleware stack:

```php
# App\Http\Kernel.php

protected $middlewareGroups = [
    'api' => [
        \Adaptdk\HttpMock\Http\Middleware\HttpMockDate::class,
    ],
];
```
