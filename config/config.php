<?php

use Adaptdk\HttpMock\Transformers\ContentTransformer;
use Adaptdk\HttpMock\Transformers\FilenameTransformer;

return [
    // The mode of operation. Valid options are:
    // - 'load': Load mock data from files.
    // - 'record': Record data and then continue to load data from files as above.
    // - null => Do nothing.,
    'data_mode' => env('HTTP_MOCK_DATA_MODE', null),

    // A comma-separated list of hosts to mock
    'hosts' => env('HTTP_MOCK_HOSTS'),

    // The directory to store mock data files (relative to project root).
    'storage_path' => env('HTTP_MOCK_STORAGE_PATH', 'tests/data'),

    // An array of whitelisted headers (used for the request hash).
    'whitelisted_headers' => [
        'Mock-Vary',
    ],
];
