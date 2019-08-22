<?php declare(strict_types=1);

namespace tiFy\Console;

use Dotenv\Dotenv;
use Symfony\Component\Console\Application as SfApplication;

/**
 * USAGE :
 * Liste des commandes disponibles
 * -------------------------------
 * vendor/bin/bee list
 *
 * Arrêt complet des commandes CLI lancées
 * ---------------------------------------
 * pkill -9 php
 */
class Application extends SfApplication
{
    /**
     * Définition des entêtes par défaut.
     *
     * @param array $argv
     *
     * @return $this
     */
    public function setDefaultHeaders($argv = []): self
    {
        // Entêtes par défaut
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';
        $_SERVER['HTTP_USER_AGENT'] = '';
        $_SERVER['REQUEST_METHOD']  = 'GET';
        $_SERVER['REMOTE_ADDR']     = '127.0.0.1';
        /** @see php -i | grep php.ini */
        $_SERVER['TZ'] = ini_get('date.timezone') ?: 'UTC';

        // Entêtes associées à l'url
        if ($url = preg_grep('/^\-\-url\=(.*)/', $argv)) {
            foreach (array_keys($url) as $k) {
                unset($argv[$k]);
            }

            $url = current($url);
            $url = preg_replace('/^\-\-url\=/', '', $url);
        } else {
            // Récupération des vendors.
            $vendor_path = __DIR__ . '/../../../../autoload.php';
            $root_path = __DIR__ . '/../../../../../';
            if (file_exists($vendor_path)) {
                require_once $vendor_path;
                $env = Dotenv::create($root_path);
                $env->load();
                $url = getenv('APP_URL') ?: '';
            }
        }

        // Entêtes associées à l'url
        $url   = $url ?: 'http://localhost';
        $parts = parse_url($url);
        if (isset($parts['host'])) {
            if (isset($parts['scheme']) && 'https' === strtolower($parts['scheme'])) {
                $_SERVER['HTTPS'] = 'on';
            }

            $_SERVER['HTTP_HOST'] = $parts['host'];
            if (isset($parts['port'])) {
                $_SERVER['HTTP_HOST'] .= ':' . $parts['port'];
            }

            $_SERVER['SERVER_NAME'] = $parts['host'];
        };

        $_SERVER['REQUEST_URI']  = ($parts['path'] ?? '') . (isset($parts['query']) ? '?' . $parts['query'] : '');
        $_SERVER['SERVER_PORT']  = $parts['port'] ?? '80';
        $_SERVER['QUERY_STRING'] = $parts['query'] ?? '';

        return $this;
    }
}