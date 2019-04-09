<?php

namespace tiFy\Contracts\Filesystem;

use League\Flysystem\FilesystemInterface;

/**
 * Interface StorageManager
 * @package tiFy\Contracts\Filesystem
 *
 * @mixin \League\Flysystem\MountManager
 */
interface StorageManager extends FilesystemInterface
{
    /**
     * Création d'une instance de l'adaptateur local.
     *
     * @param array $config Liste des paramètres de configuration.
     *
     * @return Filesystem
     */
    public function createLocal(array $config);

    /**
     * Récupération d'un point de montage.
     *
     * @param string $name Nom de qualification du point de montage.
     *
     * @return Filesystem
     */
    public function disk(string $name);

    /**
     * Déclaration d'un point de montage.
     *
     * @param string $name Nom de qualification.
     * @param array|Filesystem $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public function register(string $name, $attrs);

    /**
     * Définition d'un point de montage.
     *
     * @return static
     */
    public function set(string $name, Filesystem $filesystem);
}