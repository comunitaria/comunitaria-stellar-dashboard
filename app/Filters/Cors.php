<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Cors implements FilterInterface
{
    /**
     * @param array|null $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        /** @var ResponseInterface $response */
        $response = service('response');

        // Reflect Origin when provided, else allow all
        $origin = $request->getHeaderLine('Origin') ?: '*';
        $response->setHeader('Access-Control-Allow-Origin', $origin);
        $response->setHeader('Vary', 'Origin');

        // $response->setHeader('Access-Control-Allow-Credentials', 'true'); // enable only if you use cookies/credentials

        if ($request->is('OPTIONS')) {
            $response->setStatusCode(204);

            // Allow requested headers (covers custom headers like ngrok-skip-browser-warning)
            $allowHeaders = $request->getHeaderLine('Access-Control-Request-Headers');
            if ($allowHeaders === '') {
                $allowHeaders = 'X-API-KEY, X-Requested-With, Content-Type, Accept, Authorization, ngrok-skip-browser-warning';
            }
            $response->setHeader('Access-Control-Allow-Headers', $allowHeaders);

            // Allow requested method or a safe default set
            $allowMethod = $request->getHeaderLine('Access-Control-Request-Method');
            $response->setHeader(
                'Access-Control-Allow-Methods',
                $allowMethod !== '' ? $allowMethod : 'GET, POST, OPTIONS, PUT, PATCH, DELETE'
            );

            // Cache preflight result
            $response->setHeader('Access-Control-Max-Age', '3600');

            return $response;
        }
    }

    /**
     * @param array|null $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Ensure CORS headers present on actual responses as well
        $origin = $request->getHeaderLine('Origin') ?: '*';
        $response->setHeader('Access-Control-Allow-Origin', $origin);
        $response->setHeader('Vary', 'Origin');
    }
}