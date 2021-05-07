<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use Pollen\Debug\DebugManagerInterface;
use Pollen\Support\Proxy\ContainerProxy;
use Psr\Container\ContainerInterface as Container;

class WpDebug
{
    use ContainerProxy;

    /**
     * @var DebugManagerInterface
     */
    protected $debug;

    /**
     * @param DebugManagerInterface $debug
     * @param Container $container
     */
    public function __construct(DebugManagerInterface $debug, Container $container)
    {
        $this->debug = $debug;
        $this->setContainer($container);

        add_action(
            'wp_head',
            function () {
                if ($this->debug->debugBar()->isEnabled()) {
                    echo "<!-- DebugBar -->";
                    echo $this->debug->debugBar()->renderHead();
                    echo "<!-- / DebugBar -->";
                }
            },
            999999
        );

        add_action(
            'wp_footer',
            function () {
                if ($this->debug->debugBar()->isEnabled()) {
                    echo $this->debug->debugBar()->render();
                }
            },
            999999
        );

        add_action(
            'admin_head',
            function () {
                if ($this->debug->debugBar()->isEnabled()) {
                    echo "<!-- Debug -->";
                    echo $this->debug->debugBar()->renderHead();
                    echo "<!-- / Debug -->";
                }
            },
            999999
        );

        add_action(
            'admin_footer',
            function () {
                if ($this->debug->debugBar()->isEnabled()) {
                    echo $this->debug->debugBar()->render();
                }
            },
            999999
        );
    }
}
