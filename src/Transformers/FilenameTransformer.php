<?php

namespace Adaptdk\HttpMock\Transformers;

use Adaptdk\HttpMock\Contracts\FilenameTransformer as FilenameTransformerContract;
use Illuminate\Http\Client\Request;

class FilenameTransformer implements FilenameTransformerContract
{
    protected $whitelistedHeaders = [];

    public function _construct()
    {
        $this->whitelistedHeaders = config('http-mock.whitelisted_headers');
    }

    /**
     * @param \Illuminate\Http\Client\Request $request
     * @return string
     */
    public function transform(Request $request): string
    {
        return sprintf(
            "%s_%s_%s.json",
            basename(trim($request->url(), "/")),
            strtolower($request->method()),
            $this->generateRequestHash($request),
        );
    }

    /**
     * @param \Illuminate\Http\Client\Request $request
     * @return string
     */
    public function generateRequestHash(Request $request): string
    {
        $urlParts = parse_url($request->url());

        $params = [
            "method" => $request->method(),
            "body" => $request->body(),
            "data" => $request->data(),
            "path" => $urlParts["path"] ?? '',
            "headers" => [],
        ];

        foreach ($request->headers() as $headerName => $header) {
            if (in_array($headerName, $this->whitelistedHeaders)) {
                $params["headers"][$headerName] = $header;
            }
        }

        ksort($params["headers"]);

        return md5(serialize($params));
    }
}
