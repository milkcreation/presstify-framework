<?php

namespace tiFy\Kernel\Filesystem;

use League\Flysystem\Adapter\Local;
use Symfony\Component\Filesystem\Filesystem as SfFilesystem;
use tiFy\Kernel\Filesystem\Filesystem;

class Paths extends Filesystem
{
    /**
     * Séparateur de portion de chemin.
     * @var string
     */
    const DS = DIRECTORY_SEPARATOR;

    /**
     * Chemin absolu vers le repertoire racine du projet.
     * @var string
     */
    protected $basePath;

    /**
     * Chemin absolu vers le repertoire de stockage des fichiers de configuration.
     * @var string
     */
    protected $configPath;

    /**
     * Chemin absolu vers le répertoire publique.
     * @var string
     */
    protected $publicPath;

    /**
     * Chemin absolu vers le répertoire du thème courant.
     * @var string
     */
    protected $themePath;

    /**
     * Chemin absolu vers le repertoire racine de presstiFy.
     * @var string
     */
    protected $tiFyPath;

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(new Local(ROOT_PATH));
    }

    /**
     * {@inheritdoc}
     *
     * @return Local
     */
    public function getAdapter()
    {
        return parent::getAdapter();
    }

    /**
     * Récupération du chemin vers un répertoire ou un fichier du répetoire de base (racine).
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du repertoire.
     * @param bool $rel Activation de la sortie du chemin au format relatif.
     *
     * @return string
     */
    public function getBasePath($path = '', $rel = false)
    {
        if(!$this->basePath) :
            $this->basePath = rtrim($this->getAdapter()->getPathPrefix(), self::DS);
        endif;

        return $this->getPath($this->basePath . ($path ? self::DS . ltrim($path, self::DS) : $path), $rel);
    }

    /**
     * Récupération du chemin vers un répertoire ou un fichier du répertoire de configuration.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $rel Activation de la sortie du chemin au format relatif.
     *
     * @return string
     */
    public function getConfigPath($path = '', $rel = false)
    {
        if(!$this->configPath) :
            $this->configPath = !$this->isWpClassic()
                ? $this->getBasePath('config')
                : get_template_directory() . '/config';
        endif;

        return $this->getPath($this->configPath . ($path ? self::DS . ltrim($path, self::DS) : $path), $rel);
    }

    /**
     * Récupération du chemin vers un répertoire ou un fichier au format absolu ou relatif.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier.
     * @param bool $rel Activation de la sortie du chemin au format relatif.
     *
     * @return string
     */
    public function getPath($path, $rel = false)
    {
        return $rel
            ? $this->makeRelativePath($path)
            : $path;
    }

    /**
     * Récupération du chemin vers un répertoire ou un fichier du répertoire publique.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $rel Activation de la sortie du chemin en relatif.
     *
     * @return string
     */
    public function getPublicPath($path = '', $rel = false)
    {
        if(!$this->publicPath) :
            $this->publicPath = !$this->isWpClassic()
                ? $this->getBasePath('public')
                : rtrim(ABSPATH, self::DS);
        endif;

        return $this->getPath($this->publicPath . ($path ? self::DS . ltrim($path, self::DS) : $path), $rel);
    }

    /**
     * Récupération du chemin vers un répertoire ou un fichier du répertoire publique.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du répertoire.
     * @param bool $rel Activation de la sortie du chemin en relatif.
     *
     * @return string
     */
    public function getThemePath($path = '', $rel = false)
    {
        if(!$this->themePath) :
            $this->themePath = \get_template_directory();
        endif;

        return $this->getPath($this->themePath . ($path ? self::DS. ltrim($path, self::DS) : $path), $rel);
    }

    /**
     * Récupération du chemin vers un répertoire ou un fichier du répertoire de presstiFy.
     *
     * @param string $path Chemin relatif vers un fichier ou un dossier du repertoire.
     * @param bool $rel Activation de la sortie du chemin en relatif.
     *
     * @return string
     */
    public function getTiFyPath($path = '', $rel = false)
    {
        if(!$this->tiFyPath) :
            $this->tiFyPath = $this->getBasePath('/vendor/presstify/framework/src');
        endif;

        return $this->getPath($this->tiFyPath . ($path ? self::DS . ltrim($path, self::DS) : $path), $rel);
    }

    /**
     * Vérification du type d'arborescence du projet.
     * @internal Vraie si les répertoires de Wordpress sont à la racine du projet.
     *
     * @return bool
     */
    public function isWpClassic()
    {
        return rtrim(ABSPATH, self::DS) === $this->getBasePath();
    }

    /**
     * Convertion d'un chemin absolu en chemin relatif.
     *
     * @param string $target_path Chemin absolu vers la cible.
     * @param string $base_path Chemin absolu vers la racine.
     *
     * @return string
     */
    public function makeRelativePath($target_path, $base_path = null)
    {
        $base_path = $base_path ?? $this->getBasePath();

        $path = (new SfFilesystem())->makePathRelative($target_path, $base_path);

        return !is_dir($target_path) ? rtrim($path, self::DS) : $path;
    }
}