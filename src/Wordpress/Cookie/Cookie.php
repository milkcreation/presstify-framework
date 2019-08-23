<?php declare(strict_types=1);

namespace tiFy\Wordpress\Cookie;

use tiFy\Contracts\Cookie\{Cookie as CookieContract};

class Cookie
{
    /**
     * Instance du gestionnaire de routage.
     * @var CookieContract
     */
    protected $manager;

    /**
     * CONSTRUCTEUR.
     *
     * @param CookieContract $manager Instance du gestionnaire de cookie.
     *
     * @return void
     */
    public function __construct(CookieContract $manager)
    {
        $this->manager = $manager;

        if (!config()->has('cookie.salt')) {
            $this->manager->setSalt('_' . COOKIEHASH);
        }

        if ($cookies = config('cookie.cookies', [])) {
            foreach (config('cookie.cookies') as $k => $v) {
                is_numeric($k) ? $this->manager->instance($v) : $this->manager->instance($k, $v);
            }
        }
    }
}