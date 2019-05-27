<?php declare(strict_types=1);

namespace tiFy\Contracts\PostType;

use tiFy\Contracts\Support\Manager;

interface PostTypeManager extends Manager
{
    /**
     * Récupération de l'instance du controleur de metadonnées de post.
     *
     * @return PostTypePostMeta|null
     */
    public function post_meta(): ?PostTypePostMeta;

    /**
     * Résolution d'un service fourni par le gestionnaire.
     *
     * @param string $alias Nom de qualification du service.
     *
     * @return object
     */
    public function resolve(string $alias);
}