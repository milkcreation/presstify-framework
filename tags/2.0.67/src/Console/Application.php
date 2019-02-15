<?php

namespace tiFy\Console;

use Symfony\Component\Console\Application as SfApplication;

class Application extends SfApplication
{
    /**
     * Définition des entêtes par défaut.
     *
     * @param array $argv
     *
     * @return $this
     */
    public function setDefaultHeaders($argv = [])
    {
        // Entêtes par défaut
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';
        $_SERVER['HTTP_USER_AGENT'] = '';
        $_SERVER['REQUEST_METHOD']  = 'GET';
        $_SERVER['REMOTE_ADDR']     = '127.0.0.1';
        /** @see php -i | grep php.ini */
        $_SERVER['TZ']              = ini_get('date.timezone') ? : 'UTC';

        // Entêtes associées à l'url
        if ($url = preg_grep('/^\-\-url\=(.*)/', $argv)) :
            foreach (array_keys($url) as $k) :
                unset($argv[$k]);
            endforeach;

            $url = current($url);
            $url = preg_replace('/^\-\-url\=/', '', $url);

            $parts = parse_url($url);
            if (isset($parts['host'])) :
                if (isset($parts['scheme']) && 'https' === strtolower($parts['scheme'])) :
                    $_SERVER['HTTPS'] = 'on';
                endif;

                $_SERVER['HTTP_HOST'] = $parts['host'];
                if (isset($parts['port'])) :
                    $_SERVER['HTTP_HOST'] .= ':' . $parts['port'];
                endif;

                $_SERVER['SERVER_NAME'] = $parts['host'];
            endif;

            $_SERVER['REQUEST_URI']  = ($parts['path'] ?? '') . (isset($parts['query']) ? '?' . $parts['query'] : '');
            $_SERVER['SERVER_PORT']  = $parts['port'] ?? '80';
            $_SERVER['QUERY_STRING'] = $parts['query'] ?? '';
        else :
            define('MULTISITE', false);
        endif;

        return $this;
    }
}