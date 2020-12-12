<?php declare(strict_types=1);

namespace tiFy\Contracts\Partial;

use tiFy\Contracts\Cookie\Cookie;

interface CookieNotice extends PartialDriver
{
    /**
     * Récupération de l'instance du cookie associé.
     * 
     * @return Cookie
     */
    public function cookie(): Cookie;

    /**
     * Élement de validation du cookie.
     *
     * @param array $args Liste des attributs de configuration.
     *
     * @return string
     */
    public function trigger($args = []): string;

    /**
     * Contrôleur de traitement de la requête XHR.
     *
     * @param array ...$args Liste dynamique de variables passés en argument dans l'url de requête
     *
     * @return array
     */
    public function xhrResponse(...$args): array;
}