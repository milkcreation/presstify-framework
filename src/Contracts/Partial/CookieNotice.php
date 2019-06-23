<?php declare(strict_types=1);

namespace tiFy\Contracts\Partial;

interface CookieNotice extends PartialFactory
{
    /**
     * Élement de validation du cookie.
     *
     * @param array $args Liste des attributs de configuration.
     *
     * @return string
     */
    public function trigger($args = []): string;

    /**
     * Génération du cookie de notification via une requête XHR.
     *
     * @return array
     */
    public function xhrResponse(): array;
}