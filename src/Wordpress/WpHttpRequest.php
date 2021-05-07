<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use Pollen\Http\RequestInterface;
use Pollen\Support\Proxy\ContainerProxy;
use Psr\Container\ContainerInterface as Container;
use WP_Screen;

class WpHttpRequest
{
    use ContainerProxy;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param RequestInterface $request
     * @param Container $container
     */
    public function __construct(RequestInterface $request, Container $container)
    {
        $this->request = $request;
        $this->setContainer($container);

        add_action('current_screen', function (WP_Screen $wp_screen) {
            $this->request->attributes->set('wp_screen', new WpScreen($wp_screen));
        }, 0);
    }
}