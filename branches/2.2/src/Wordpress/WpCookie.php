<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use Pollen\Cookie\CookieJarInterface;
use WP_Site;

class WpCookie
{
    /**
     * @var CookieJarInterface
     */
    protected $cookieJar;

    /**
     * @param CookieJarInterface $cookieJar
     *
     * @return void
     */
    public function __construct(CookieJarInterface $cookieJar)
    {
        $this->cookieJar = $cookieJar;

        if (is_multisite() && $site = WP_Site::get_instance(get_current_blog_id())) {
            $domain = config()->get('cookie.domain') ?? $site->domain;
            $path = config()->get('cookie.path') ?? $site->path;

            $this->cookieJar->setDefaults($path, $domain);

            if (!config()->has('cookie.salt')) {
                $this->cookieJar->setSalt(
                    '_' . md5($this->cookieJar->domain . $this->cookieJar->path . COOKIEHASH)
                );
            }
        } else {
            if (!config()->has('cookie.salt')) {
                $this->cookieJar->setSalt('_' . COOKIEHASH);
            }
        }

        if ($cookies = config('cookie.cookies', [])) {
            foreach ($cookies as $k => $v) {
                is_numeric($k) ? $this->cookieJar->make($v) : $this->cookieJar->make($k, $v);
            }
        }
    }
}