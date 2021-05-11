<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use Pollen\Session\SessionManagerInterface;
use Pollen\Support\Proxy\ContainerProxy;
use Pollen\Support\Proxy\HttpRequestProxy;
use Psr\Container\ContainerInterface as Container;
use RuntimeException;

class WpSession
{
    use ContainerProxy;
    use HttpRequestProxy;

    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * @param SessionManagerInterface $session
     * @param Container $container
     */
    public function __construct(SessionManagerInterface $session, Container $container)
    {
        $this->session = $session;
        $this->setContainer($container);

        /*
        events()->on('session.read', function (TriggeredEventInterface $event, &$value) {
            $value = Arr::stripslashes($value);
        });
        */
    }
}