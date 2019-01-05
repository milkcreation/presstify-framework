<?php

namespace tiFy\Contracts\Partial;

interface CookieNotice extends PartialController
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
     * Génération du cookie de notification via Ajax.
     *
     * @return void
     */
    public function wp_ajax();
}