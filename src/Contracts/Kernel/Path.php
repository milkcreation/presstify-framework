<?php declare(strict_types=1);

namespace tiFy\Contracts\Kernel;

use tiFy\Contracts\Filesystem\Filesystem;
use tiFy\Contracts\Filesystem\StorageManager;

interface Path extends StorageManager
{
    /**
     * {@inheritDoc}
     *
     * @return Filesystem|null
     */
    public function getFilesystem($prefix): ?Filesystem;

    /**
     * Récupération de l'instance du gestionnaire de dossier racine.
     *
     * @return Filesystem
     */
    public function diskBase(): Filesystem;

    /**
     * Récupération de l'instance du gestionnaire du dossier de stockage des fichiers de cache.
     *
     * @return Filesystem
     */
    public function diskCache(): Filesystem;

    /**
     * Récupération de l'instance du gestionnaire du dossier de stockage des fichiers de configuration.
     *
     * @return Filesystem
     */
    public function diskConfig(): Filesystem;

    /**
     * Récupération de l'instance du gestionnaire du dossier de stockage des fichiers de journalisation.
     *
     * @return Filesystem
     */
    public function diskLog(): Filesystem;

    /**
     * Récupération du chemin de gestionnaire de fichiers par rapport à la racine.
     *
     * @param Filesystem $disk
     * @param string $path Chemin absolue vers un dossier ou un fichier du gestionnaire.
     * @param boolean $absolute Activation de la récupération du chemin en absolu.
     *
     * @return string
     */
    public function diskPathFromBase(Filesystem $disk, string $path = '', bool $absolute = true): ?string;

    /**
     * Récupération de l'instance du gestionnaire du dossier publique.
     *
     * @return Filesystem
     */
    public function diskPublic(): Filesystem;

    /**
     * Récupération de l'instance du gestionnaire du dossier de stockage.
     *
     * @return Filesystem
     */
    public function diskStorage(): Filesystem;

    /**
     * Récupération de l'instance du gestionnaire du dossier du theme.
     *
     * @return Filesystem
     */
    public function diskTheme(): Filesystem;

    /**
     * Récupération de l'instance du gestionnaire du dossier du framework PresstiFy.
     *
     * @return Filesystem
     */
    public function diskTiFy(): Filesystem;

    /**
     * Récupération du chemin vers un dossier ou un fichier du répertoire de base (racine).
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du repertoire.
     * @param bool $rel Activation de la sortie du chemin au format relatif.
     *
     * @return string
     */
    public function getBasePath(string $path = '', bool $rel = false): string;

    /**
     * Récupération du chemin vers un dossier ou un fichier du répertoire de cache.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $rel Activation de la sortie du chemin au format relatif.
     *
     * @return string
     */
    public function getCachePath(string $path = '', bool $rel = false): string;

    /**
     * Récupération du chemin vers un dossier ou un fichier du répertoire de configuration.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $rel Activation de la sortie du chemin au format relatif.
     *
     * @return string
     */
    public function getConfigPath(string $path = '', bool $rel = false): string;

    /**
     * Récupération du chemin vers un dossier ou un fichier du répertoire de journalisation.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $rel Activation de la sortie du chemin au format relatif.
     *
     * @return string
     */
    public function getLogPath(string $path = '', bool $rel = false): string;

    /**
     * Récupération du chemin vers un dossier ou un fichier du répertoire publique.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $rel Activation de la sortie du chemin en relatif.
     *
     * @return string
     */
    public function getPublicPath(string $path = '', bool $rel = false): string;

    /**
     * Récupération du chemin vers un dossier ou un fichier du répertoire du thème.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $rel Activation de la sortie du chemin en relatif.
     *
     * @return string
     */
    public function getThemePath(string $path = '', bool $rel = false): string;

    /**
     * Récupération du chemin vers un dossier ou un fichier du répertoire du framework presstiFy.
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
    public function isWp(): bool;

    /**
     * Création d'un gestionnaire de disque.
     *
     * @param string $name Nom de qualification.
     * @param string $root Chemin absolu vers la racine du disque.
     * @param array $config Configuration.
     *
     * @return Filesystem
     */
    public function mount(string $name, string $root, array $config = []): Filesystem;

    /**
     * Récupération du chemin par rapport à la racine.
     *
     * @param string $pathname Chemin absolue vers un dossier ou un fichier.
     *
     * @return string
     */
    public function relPathFromBase(string $pathname): ?string;
}