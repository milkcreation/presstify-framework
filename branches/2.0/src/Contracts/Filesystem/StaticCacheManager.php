<?php declare(strict_types=1);

namespace tiFy\Contracts\Filesystem;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface StaticCacheManager extends StorageManager
{
    /**
     * Récupération de la réponse HTTP.
     *
     * @param string $path Chemin relatif vers un fichier source.
     * @param ServerRequestInterface $psrRequest Requête Psr.
     *
     * @return StreamedResponse
     */
    public function getResponse(string $path, ServerRequestInterface $psrRequest): StreamedResponse;

    /**
     * Vérifie si le système de cache est prêt.
     *
     * @return boolean
     */
    public function ready(): bool;

    /**
     * Récupération du gestionnaire des ressources en cache.
     *
     * @return Filesystem|null
     */
    public function getCache(): ?Filesystem;

    /**
     * Récupération du gestionnaire des ressources originales.
     *
     * @return Filesystem|null
     */
    public function getSource(): ?Filesystem;

    /**
     * Définition du gestionnaire des ressources en cache.
     *
     * @param Filesystem $cache
     *
     * @return static
     */
    public function setCache(Filesystem $cache): StaticCacheManager;

    /**
     * Définition du gestionnaire des ressources originales.
     *
     * @param Filesystem $source
     *
     * @return static
     */
    public function setSource(Filesystem $source): StaticCacheManager;
}