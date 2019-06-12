<?php declare(strict_types=1);

namespace tiFy\Contracts\Partial;

interface CookieNotice extends PartialFactory
{
    /**
     * Récupération d'un cookie.
     *
     * @return string|null
     */
    public function getCookie(): ?string;

    /**
     * Récupération de la liste des arguments de génération de cookie.
     *
     * @param string $name Nom de qualification du cookie.
     * @param string|null $value Valeur du cookie.
     * @param int $expire Nombre de secondes jusqu'à l'expiration du cookie.
     *
     * @return array
     */
    public function getCookieArgs(string $name, ?string $value = null, int $expire = 0): array;

    /**
     * Définition d'un cookie.
     *
     * @param string $name Nom de qualification du cookie.
     * @param string|null $value Valeur du cookie.
     * @param int $expire Nombre de secondes jusqu'à l'expiration du cookie.
     *
     * @return void
     */
    public function setCookie(string $name, ?string $value = null, int $expire = 0);

    /**
     * Génération du cookie de notification via une requête XHR.
     *
     * @return array
     */
    public function xhrResponse(): array;
}