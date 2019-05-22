<?php declare(strict_types=1);

namespace tiFy\Contracts\Template;

use League\Flysystem\{FilesystemInterface, MountManager};
use Psr\Http\Message\ServerRequestInterface;
use tiFy\Contracts\Filesystem\Filesystem;

/**
 * @mixin MountManager
 */
interface FactoryCache extends FactoryAwareTrait, FilesystemInterface
{
    /**
     * Récupération d'une ressource en cache.
     *
     * @param string Chemin relatif vers la ressource.
     * @param ServerRequestInterface $psrRequest Instance de la requête Psr.
     *
     * @return mixed
     */
    public function getResponse(string $path, ServerRequestInterface $psrRequest);

    /**
     * Vérifie si l'instance est opérationnelle.
     *
     * @return boolean
     */
    public function ready(): bool;

    /**
     * Définition du gestionnaire des ressources en cache.
     *
     * @param Filesystem $cache
     *
     * @return $this
     */
    public function setCache(Filesystem $cache): FactoryCache;

    /**
     * Définition du gestionnaire des ressources originales.
     *
     * @param Filesystem $source
     *
     * @return $this
     */
    public function setSource(Filesystem $source): FactoryCache;
}