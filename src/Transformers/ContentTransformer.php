<?php

namespace Adaptdk\HttpMock\Transformers;

use Adaptdk\HttpMock\Contracts\ContentTransformer as ContentTransformerContract;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Client\Request;

class ContentTransformer implements ContentTransformerContract
{
    /**
     * @param string $content
     * @return string
     */
    public function transform(Request $request, Response $response): array
    {
        // Generate recording structure.
        return [
            "method" => $request->method(),
            "statusCode" => $response->getStatusCode(),
            "reasonPhrase" => $response->getReasonPhrase(),
            "protocolVersion" => $response->getProtocolVersion(),
            // Add all headers except Date as this will generate an annoying diff every time it is re-recorded.
            "headers" => array_diff_key(
                $response->getHeaders(),
                ["Date" => null]
            ),
            "body" => base64_encode((string) $response->getBody()),
        ];
    }
}
