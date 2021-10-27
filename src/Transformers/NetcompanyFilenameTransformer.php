<?php

namespace Adaptdk\HttpMock\Transformers;

use Illuminate\Http\Client\Request;

class NetcompanyFilenameTransformer extends FilenameTransformer
{
    /**
     * @param \Illuminate\Http\Client\Request $request
     * @return string
     */
    public function transform(Request $request): string
    {
        return sprintf(
            "user_%s/%s_%s_%s.json",
            $this->getPersonHash($request),
            $this->getEndpointLabel($request),
            strtolower($request->method()),
            $this->generateRequestHash($request),
        );
    }

    /**
     * @param \Illuminate\Http\Client\Request $request
     * @return string
     */
    protected function getEndpointLabel(Request $request): string
    {
        // Eliminate all uuid's and anything after that.
        $path = preg_replace('/\/[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}.*/i', '', $request->url());

        // Eliminate all nemid's and anything after that.
        $path = preg_replace('/\/[0-9]{4}\-[0-9]{4}\-[0-9]\-[0-9]{12}.*/i', '', $path);

        // Eliminate all query parameters.
        $path = preg_replace('/\?.*/i', '', $path);

        // Then use the basename as the endoint label.
        return basename(trim($path, "/"));
    }

    /**
     * @param \Illuminate\Http\Client\Request $request
     * @return string
     */
    protected function getPersonHash(Request $request): string
    {
        return $request->hasHeader("Selvbetjening-ID-Person")
            ? md5(serialize($request->header("Selvbetjening-ID-Person")))
            : "unauthenticated";
    }
}
