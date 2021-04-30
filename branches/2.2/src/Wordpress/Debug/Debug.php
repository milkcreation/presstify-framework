<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Debug;

use Exception;
use tiFy\Contracts\Debug\Debug as DebugManager;
use tiFy\Support\Env;

class Debug
{
    /**
     * Instance du gestionnaire de deboguage.
     * @var DebugManager
     */
    protected $debugManager;

    /**
     * @param DebugManager $debugManager
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct(DebugManager $debugManager)
    {
        $this->debugManager = $debugManager;

        if (Env::isDev()) {
            add_action('wp_head', function () {
                echo "<!-- Debug -->";
                echo $this->debugManager->boot()->getHead();
                echo "<!-- / Debug -->";
            }, 999999);

            add_action('wp_footer', function () {
                echo $this->debugManager->render();
            }, 999999);

            add_action('admin_head', function () {
                echo "<!-- Debug -->";
                echo $this->debugManager->boot()->getHead();
                echo "<!-- / Debug -->";
            }, 999999);

            add_action('admin_footer', function () {
                echo $this->debugManager->render();
            }, 999999);
        }
    }
}
