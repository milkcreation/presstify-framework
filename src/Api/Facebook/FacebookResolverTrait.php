<?php

/**
 * @see https://github.com/facebook/php-graph-sdk
 * @see https://developers.facebook.com/docs/php/howto/example_facebook_login
 */

namespace tiFy\Api\Facebook;

use tiFy\Api\Facebook\Facebook;

trait FacebookResolverTrait
{
    /**
     * Instance du controleur principal.
     *
     * @return Facebook
     */
    public function fb()
    {
        return app('api.facebook');
    }
}