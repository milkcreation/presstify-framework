<?php declare(strict_types=1);

namespace tiFy\Contracts\Template;

use Psr\Container\ContainerInterface;
use tiFy\Contracts\Container\Container;
use tiFy\Contracts\Support\Manager;

interface TemplateManager extends Manager
{
    /**
     * {@inheritDoc}
     *
     * @return Container
     */
    public function getContainer(): ContainerInterface;

    /**
     * Récupération du chemin absolu vers le répertoire de stockage des ressources.
     *
     * @param string $path Chemin relatif vers une ressource du répertoire (fichier ou dossier).
     *
     * @return string
     */
    public function resourcesDir(?string $path = ''): ?string;

    /**
     * Récupération de l'url absolue vers le répertoire de stockage des ressources.
     *
     * @param string $path Chemin relatif vers une ressource du répertoire (fichier ou dossier).
     *
     * @return string
     */
    public function resourcesUrl(?string $path = ''): ?string;
}