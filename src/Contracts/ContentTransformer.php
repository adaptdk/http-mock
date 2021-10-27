<?php

namespace Adaptdk\HttpMock\Contracts;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Client\Request;

interface ContentTransformer
{
    /**
     * Transform the given data.
     *
     * @param  \Illuminate\Http\Client\Request  $request
     * @param  \GuzzleHttp\Psr7\Response  $response
     */
    public function transform(Request $request, Response $response): array;
}
