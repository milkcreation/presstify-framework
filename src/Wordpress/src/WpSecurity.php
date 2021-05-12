<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

class WpSecurity
{
    public function __construct()
    {
        if(config('wp-login-redirect.enabled', false)) {
            new WpLoginRedirect(config('wp-login-redirect.endpoints', []));
        }
    }
}