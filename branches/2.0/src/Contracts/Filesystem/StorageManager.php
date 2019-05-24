<?php declare(strict_types=1);

namespace tiFy\Contracts\Filesystem;

use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\FilesystemNotFoundException;
use League\Flysystem\MountManager;
use tiFy\Contracts\Container\Container;

/**
 * Interface StorageManager
 * @package tiFy\Contracts\Filesystem
 *
 * @mixin MountManager
 */
interface StorageManager extends FilesystemInterface
{
    /**
     * Création d'une instance de disque local.
     *
     * @param string $root Chemin absolu vers le répertoire du stockage de fichiers.
     * @param array $config Liste des paramètres de configuration.
     *
     * @return Filesystem
     */
    public function createLocal(string $root, array $config): Filesystem;

    /**
     * Récupération d'un point de montage.
     *
     * @param string $name Nom de qualification du disque.
     *
     * @return Filesystem
     */
    public function disk(string $name): Filesystem;

    /**
     * Récupération de l'instance du controleur d'injection de dépendances.
     *
     * @return Container|null
     */
    public function getContainer(): ?Container;

    /**
     * Récupération d'une instance gestionnaire de fichier.
     *
     * @param string $name Nom de qualification du disque.
     *
     * @return Filesystem
     *
     * @throws FilesystemNotFoundException
     *
     */
    public function getFilesystem($name);

    /**
     * Montage d'une instance de gestionnaire de fichiers.
     *
     * @param string $name Nom de qualification du disque.
     * @param FilesystemInterface $filesystem Instance du gestionnaire de fichiers.
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function mountFilesystem($name, FilesystemInterface $filesystem);

    /**
     * Déclaration d'un disque.
     *
     * @param string $name Nom de qualification.
     * @param array|Filesystem $attrs Liste des attributs de configuration|Instance du gestionnaire de fichiers.
     *
     * @return $this
     */
    public function register(string $name, $attrs): StorageManager;

    /**
     * Définition d'un disque.
     *
     * @param string $name Nom de qualification.
     * @param Filesystem $filesystem Instance du gestionnaire de fichiers.
     *
     * @return $this
     */
    public function set(string $name, Filesystem $filesystem): StorageManager;
}