<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use Pollen\Cookie\CookieInterface;
use tiFy\Partial\PartialDriverInterface;

interface CookieNoticeDriverInterface extends PartialDriverInterface
{
    /**
     * Récupération de l'instance du cookie associé.
     * 
     * @return CookieInterface
     */
    public function cookie(): CookieInterface;

    /**
     * Élement de validation du cookie.
     *
     * @param array $args Liste des attributs de configuration.
     *
     * @return string
     */
    public function trigger(array $args = []): string;
}