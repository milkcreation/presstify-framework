<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use Pollen\Session\SessionManagerInterface;
use Pollen\Support\Proxy\HttpRequestProxy;
use RuntimeException;

class WpSession
{
    use HttpRequestProxy;

    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * @param SessionManagerInterface $session
     *
     * @return void
     */
    public function __construct(SessionManagerInterface $session)
    {
        $this->session = $session;

        try {
            $session->start();

            $this->httpRequest()->setSession($session->processor());
        } catch (RuntimeException $e) {
            unset($e);
        }

        /*
        events()->on('session.read', function (TriggeredEventInterface $event, &$value) {
            $value = Arr::stripslashes($value);
        });
        */
    }
}