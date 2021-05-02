<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use Pollen\Http\RequestInterface;
use WP_Screen;

class WpHttpRequest
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;

        add_action('current_screen', function (WP_Screen $wp_screen) {
            $this->request->attributes->set('wp_screen', new WpScreen($wp_screen));
        }, 0);
    }
}