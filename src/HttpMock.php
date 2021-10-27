<?php

namespace Adaptdk\HttpMock;

use Adaptdk\HttpMock\Contracts\ContentTransformer;
use Adaptdk\HttpMock\Contracts\FilenameTransformer;
use Adaptdk\HttpMock\Exceptions\ContentTransformerNotFound;
use Adaptdk\HttpMock\Exceptions\FilenameTransformerNotFound;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File as FileStorage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HttpMock
{
    protected $mode;
    protected $storagePath;

    public function __construct()
    {
        $this->mode = config('http-mock.data_mode');
        $this->storagePath = base_path(config('http-mock.storage_path'));
    }

    static public function register()
    {
        (new static())->fake();
    }

    public function fake()
    {
        if (!config('http-mock.data_mode')) {
            return;
        }

        $hosts = [];

        foreach (explode(',', config('http-mock.hosts')) as $host) {
            $hosts[$host . '/*'] = function (Request $request) {
                return $this->mockRequest($request);
            };
        }

        Http::fake($hosts);
    }

    /**
     * @param \Illuminate\Http\Client\Request $request
     * @return \GuzzleHttp\Promise\FulfilledPromise
     */
    protected function mockRequest(Request $request): FulfilledPromise
    {
        $filename = $this->getFileName($request);

        if ($this->mode === 'record') {
            $this->recordMockdata($request, $this->storagePath . '/' . $filename);
        }

        $string = file_get_contents($filename);
        $responseData = json_decode($string, true);

        return Http::response(
            base64_decode($responseData["body"]) ?: $responseData["body"],
            $responseData["statusCode"],
            // Re-attach Date header as this is filtered out when writing the file.
            $responseData['headers'] + ["Date" => [now()->toRfc7231String()]]
        );
    }

    /**
     * @param \Illuminate\Http\Client\Request $request
     * @param string $filePath
     * @return bool
     */
    protected function recordMockdata(Request $request, string $filePath): bool
    {
        if (!in_array(app()->environment(), ["local", "testing"])) {
            Log::error("We only want to save mock files on local environments!!", $filePath);
            return false;
        }

        // Use GuzzleHttpClient to bypass the faker when recording request.
        $guzzleHttpClient = new GuzzleHttpClient;

        try {
            // Perform the request that faker caught.
            $response = $guzzleHttpClient->send($request->toPsrRequest());
        } catch (ClientException $clientException) {
            // Guzzle fires exceptions more generously than Http. Just pass the response right through.
            $response = $clientException->getResponse();
        } catch (RequestException $requestException) {
            // Guzzle fires exceptions more generously than Http. Just pass the response right through.
            $response = $requestException->getResponse();
        }

        // Ensure directories.
        if (!FileStorage::exists(dirname($filePath))) {
            FileStorage::makeDirectory(dirname($filePath), 0777, true, true);
            FileStorage::chmod(dirname($filePath), 0777);
        }

        $contents = $this->getContent($request, $response);

        // Write recorded data for later mocking.
        if (@file_put_contents($filePath, json_encode($contents, JSON_PRETTY_PRINT))) {
            FileStorage::chmod($filePath, 0777);
            return true;
        }

        return false;
    }

    /**
     * @param \Illuminate\Http\Client\Request $request
     * @return string
     */
    protected function getFileName(Request $request): string
    {
        return App::make(FilenameTransformer::class)->transform($request);
    }

    /**
     * @param \Illuminate\Http\Client\Request $request
     * @param Response $response
     * @return array
     */
    protected function getContent(Request $request, Response $response): array
    {
        return App::make(ContentTransformer::class)->transform($request, $response);
    }
}
