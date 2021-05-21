<?php

declare(strict_types=1);

use League\Uri\Contracts\UriInterface as LeagueUri;
use Psr\Http\Message\UriInterface;
use Pollen\Event\EventDispatcherInterface;
use Pollen\Http\RedirectResponseInterface;
use Pollen\Support\ClassInfo;
use Pollen\Support\ClassLoader;
use tiFy\Contracts\Cron\CronJob;
use tiFy\Contracts\Cron\CronManager;
use tiFy\Contracts\Routing\Url;

if (!function_exists('class_info')) {
    /**
     * ClassInfo - Gestionnaire de classes.
     *
     * @param string|object Nom complet ou instance de la classe.
     *
     * @return ClassInfo
     */
    function class_info($class): ClassInfo
    {
        return new ClassInfo($class);
    }
}

if (!function_exists('class_loader')) {
    /**
     * ClassLoader - Gestionnaire de déclaration d'espaces de nom et d'inclusion de fichier automatique.
     *
     * @return ClassLoader
     */
    function class_loader(): ClassLoader
    {
        return app('class-loader');
    }
}

if (!function_exists('cron')) {
    /**
     * Cron - Gestionnaire de tâches planifiées.
     *
     * @param string|null $name Nom de qualification de la tâche déclarée.
     *
     * @return CronManager|CronJob|null
     */
    function cron(?string $name = null)
    {
        /* @var CronManager $manager */
        $manager = app('cron');

        if (is_null($name)) {
            return $manager;
        }
        return $manager->get($name);
    }
}

if (!function_exists('events')) {
    /**
     * Instance du répartiteur d'événements.
     *
     * @return EventDispatcherInterface
     */
    function events(): EventDispatcherInterface
    {
        return app(EventDispatcherInterface::class);
    }
}

if (!function_exists('redirect')) {
    /**
     * HTTP - Gestionnaire de redirection HTTP.
     *
     * @param string|null $to Url de redirection.
     * @param int $status Code de la redirection.
     * @param array $headers Liste des entête HTTP
     * @param bool $secure Activation de la sécurisation
     *
     * @return RedirectResponseInterface
     */
    function redirect(?string $to = null, int $status = 302, array $headers = [], ?bool $secure = null)
    {
        if (is_null($to)) {
            return app('redirect');
        }
        return app('redirect')->to($to, $status, $headers, $secure);
    }
}



if (!function_exists('url')) {
    /**
     * Récupération de l'instance du contrôleur d'url.
     *
     * @param UriInterface|LeagueUri|string|null $uri
     *
     * @return Url
     */
    function url($uri = null): Url
    {
        /** @var Url $url */
        $url = app('url');

        return is_null($uri) ? $url : $url->set($uri);
    }
}
