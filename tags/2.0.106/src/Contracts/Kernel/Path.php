<?php declare(strict_types=1);

namespace tiFy\Contracts\Kernel;

use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use tiFy\Contracts\Filesystem\Filesystem;

interface Path extends Filesystem
{
    /**
     * {@inheritdoc}
     *
     * @return Local
     */
    public function getAdapter(): AdapterInterface;

    /**
     * Récupération du chemin vers un répertoire ou un fichier du répetoire de base (racine).
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du repertoire.
     * @param bool $rel Activation de la sortie du chemin au format relatif.
     *
     * @return string
     */
    public function getBasePath(string $path = '', bool $rel = false): string;

    /**
     * Récupération du chemin vers un répertoire ou un fichier du répertoire de configuration.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $rel Activation de la sortie du chemin au format relatif.
     *
     * @return string
     */
    public function getConfigPath(string $path = '', bool $rel = false): string;

    /**
     * Récupération du chemin vers un répertoire ou un fichier du répertoire de stockage des rapports de journalisation.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $rel Activation de la sortie du chemin au format relatif.
     *
     * @return string
     */
    public function getLogPath(string $path = '', bool $rel = false): string;


    /**
     * Récupération du chemin vers un répertoire ou un fichier au format absolu ou relatif.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier.
     * @param bool $rel Activation de la sortie du chemin au format relatif.
     *
     * @return string
     */
    public function getPath(string $path = '', bool $rel = false): string;

    /**
     * Récupération du chemin vers un répertoire ou un fichier du répertoire publique.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $rel Activation de la sortie du chemin en relatif.
     *
     * @return string
     */
    public function getPublicPath(string $path = '', bool $rel = false): string;

    /**
     * Récupération du chemin vers un répertoire ou un fichier du répertoire publique.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $rel Activation de la sortie du chemin en relatif.
     *
     * @return string
     */
    public function getThemePath(string $path = '', bool $rel = false): string;

    /**
     * Récupération du chemin vers un répertoire ou un fichier du répertoire de presstiFy.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du repertoire.
     * @param bool $rel Activation de la sortie du chemin en relatif.
     *
     * @return string
     */
    public function getTiFyPath(string $path = '', bool $rel = false): string;

    /**
     * Vérification du type d'arborescence du projet.
     * @internal Vraie si les répertoires de Wordpress sont à la racine du projet.
     *
     * @return boolean
     */
    public function isWpClassic(): bool;

    /**
     * Convertion d'un chemin absolu en chemin relatif.
     *
     * @param string $target_path Chemin absolu vers la cible.
     * @param string|null $base_path Chemin absolu vers la racine.
     *
     * @return string
     */
    public function makeRelativePath(string $target_path, ?string $base_path = null): string;
}