<?php declare(strict_types=1);

namespace tiFy\Contracts\Partial;

interface CookieNotice extends PartialFactory
{
    /**
     * Récupération d'un cookie.
     *
     * @return string
     */
    public function getCookie();

    /**
     * Définition d'un cookie.
     *
     * @var string $name Nom de qualification du cookie.
     * @var int $cookie_expire Temps avant l'expiration du cookie. Exprimé en secondes.
     *
     * @return void
     */
    public function setCookie($cookie_name, $cookie_expire = 0);

    /**
     * Génération du cookie de notification via une requête XHR.
     *
     * @return void
     */
    public function xhrSetCookie();
}