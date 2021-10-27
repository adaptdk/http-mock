<?php

namespace Adaptdk\HttpMock\Contracts;

use Illuminate\Http\Client\Request;

interface FilenameTransformer
{
    /**
     * Transform the given data.
     *
     * @param  \Illuminate\Http\Client\Request  $request
     */
    public function transform(Request $request): string;

    /**
     * Generate a unique hash for the given request.
     *
     * @param  \Illuminate\Http\Client\Request  $request
     */
    public function generateRequestHash(Request $request): string;
}
