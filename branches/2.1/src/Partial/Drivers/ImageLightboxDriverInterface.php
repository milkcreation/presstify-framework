<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use tiFy\Partial\Drivers\ImageLightbox\ImageLightboxItemInterface;
use tiFy\Partial\PartialDriverInterface;

interface ImageLightboxDriverInterface extends PartialDriverInterface
{
    /**
     * Récupération de l'instance d'un élément.
     *
     * @param ImageLightboxItemInterface|array|string|int $item
     *
     * @return ImageLightboxItemInterface|null
     */
    public function fetchItem($item): ?ImageLightboxItemInterface;

    /**
     * Traitement de la liste des éléments à afficher.
     *
     * @return static
     */
    public function parseItems(): ImageLightboxDriverInterface;
}
