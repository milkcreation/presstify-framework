<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers\ImageLightbox;

use tiFy\Contracts\Support\ParamsBag;

interface ImageLightboxItemInterface extends ParamsBag
{
    /**
     * Récupération des attributs HTML du lien.
     *
     * @param bool $linearize
     *
     * @return array|string
     */
    public function getAttrs(bool $linearize = true);

    /**
     * Récupération du groupe associé.
     *
     * @return string|null
     */
    public function getGroup(): ?string;

    /**
     * Affichage de la miniature.
     *
     * @return string
     */
    public function getContent(): string;
}