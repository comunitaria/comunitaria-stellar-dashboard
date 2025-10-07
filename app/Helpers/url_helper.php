<?php
use CodeIgniter\HTTP\SiteURI;
use Config\Services;

// Override base_url to force http when requested with 'https' in development
if (! function_exists('base_url')) {
    /**
     * Returns the base URL as defined by the App config.
     * When FORCE_HTTP=true and $scheme === 'https', switch to 'http'.
     * Also normalize paths that start with 'public/' to support spark serve (docroot=public).
     *
     * @param array|string $relativePath
     * @param string|null  $scheme
     */
    function base_url($relativePath = '', ?string $scheme = null): string
    {
        if (env('FORCE_HTTP', false) && $scheme === 'https') {
            $scheme = 'http';
        }
        if (is_string($relativePath) && str_starts_with($relativePath, 'public/')) {
            $relativePath = substr($relativePath, 7);
        }

        $currentURI = Services::request()->getUri();
        assert($currentURI instanceof SiteURI);

        return $currentURI->baseUrl($relativePath, $scheme);
    }
}
