<?php declare(strict_types=1);

namespace tiFy\Contracts\Filesystem;

use League\Flysystem\AdapterInterface;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem as LeagueFilesystem;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Interface Filesystem
 * @package tiFy\Contracts\Filesystem
 *
 * @mixin LeagueFilesystem
 */
interface Filesystem extends FilesystemInterface
{
    /**
     * Génération de la réponse statique d'un fichier.
     *
     * @param string $path Chemin relatif vers un fichier du disque.
     * @param string|null $name Nom de qualification du fichier.
     * @param array|null $headers Liste des entêtes de la réponse.
     * @param int $expires Délai d'expiration du cache en secondes. 1 an par défaut.
     * @param array $cache Paramètres complémentaire du cache.
     * @see \Symfony\Component\HttpFoundation\BinaryFileResponse::setCache()
     *
     * @return StreamedResponse
     *
     * @throws FileNotFoundException
     */
    public function binary(
        string $path,
        ?string $name = null,
        array $headers = [],
        int $expires = 31536000,
        array $cache = []
    ): BinaryFileResponse;

    /**
     * Génération de la réponse de téléchargement d'un fichier.
     *
     * @param string $path Chemin relatif vers un fichier du disque.
     * @param string|null $name Nom de qualification du fichier.
     * @param array|null $headers Liste des entêtes de la réponse.
     *
     * @return StreamedResponse
     *
     * @throws FileNotFoundException
     */
    public function download(string $path, ?string $name = null, array $headers = []): StreamedResponse;

    /**
     * Récupération de l'adaptateur "réel", lorsque celui-ci est englobé dans un système de cache.
     *
     * @return AdapterInterface
     */
    public function getRealAdapter(): AdapterInterface;

    /**
     * Récupération du chemin absolu associé à un chemin relatif.
     *
     * @param string $path Chemin relatif.
     *
     * @return string|null
     */
    public function path($path): ?string;

    /**
     * Génération de la réponse d'un fichier.
     *
     * @param string $path Chemin relatif vers un fichier du disque.
     * @param string|null $name Nom de qualification du fichier.
     * @param array|null $headers Liste des entêtes de la réponse.
     * @param string|null $disposition inline (affichage)|attachment (téléchargement).
     *
     * @return StreamedResponse
     *
     * @throws FileNotFoundException
     */
    public function response(
        string $path,
        ?string $name = null,
        array $headers = [],
        $disposition = 'inline'
    ): StreamedResponse;
}