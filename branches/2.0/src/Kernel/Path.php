<?php declare(strict_types=1);

namespace tiFy\Kernel;

use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use Symfony\Component\Filesystem\Filesystem as SfFilesystem;
use tiFy\Contracts\Kernel\Path as PathContract;
use tiFy\Filesystem\Filesystem;

class Path extends Filesystem implements PathContract
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
     * Chemin absolu vers le répertoire de stockage des fichiers de configuration.
     * @var string
     */
    protected $configPath;

    /**
     * Chemin absolu vers le répertoire publique.
     * @var string
     */
    protected $publicPath;

    /**
     * Chemin absolu vers le répertoire de stockage des fichiers de journalisation.
     * @var string
     */
    protected $logPath;

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
    public function getAdapter(): AdapterInterface
    {
        return parent::getAdapter();
    }

    /**
     * @inheritdoc
     */
    public function getBasePath(string $path = '', bool $rel = false): string
    {
        if(!$this->basePath) {
            $this->basePath = rtrim($this->getAdapter()->getPathPrefix(), self::DS);
        }
        return $this->getPath($this->basePath . ($path ? self::DS . ltrim($path, self::DS) : $path), $rel);
    }

    /**
     * @inheritdoc
     */
    public function getConfigPath(string $path = '', bool $rel = false): string
    {
        if(!$this->configPath) {
            $this->configPath = !$this->isWpClassic()
                ? $this->getBasePath('config')
                : get_template_directory() . '/config';
        }
        return $this->getPath($this->configPath . ($path ? self::DS . ltrim($path, self::DS) : $path), $rel);
    }

    /**
     * @inheritdoc
     */
    public function getLogPath(string $path = '', bool $rel = false): string
    {
        if(!$this->logPath) {
            $this->logPath = WP_CONTENT_DIR . '/uploads/log';
        }
        return $this->getPath($this->logPath . ($path ? self::DS . ltrim($path, self::DS) : $path), $rel);
    }


    /**
     * @inheritdoc
     */
    public function getPath(string $path = '', bool $rel = false): string
    {
        return $rel ? $this->makeRelativePath($path) : $path;
    }

    /**
     * @inheritdoc
     */
    public function getPublicPath(string $path = '', bool $rel = false): string
    {
        if(!$this->publicPath) {
            $this->publicPath = !$this->isWpClassic()
                ? $this->getBasePath('public')
                : rtrim(ABSPATH, self::DS);
        }
        return $this->getPath($this->publicPath . ($path ? self::DS . ltrim($path, self::DS) : $path), $rel);
    }

    /**
     * @inheritdoc
     */
    public function getThemePath(string $path = '', bool $rel = false): string
    {
        if(!$this->themePath) {
            $this->themePath = get_template_directory();
        }
        return $this->getPath($this->themePath . ($path ? self::DS. ltrim($path, self::DS) : $path), $rel);
    }

    /**
     * @inheritdoc
     */
    public function getTiFyPath(string $path = '', bool $rel = false): string
    {
        if(!$this->tiFyPath) {
            $this->tiFyPath = $this->getBasePath('/vendor/presstify/framework/src');
        }
        return $this->getPath($this->tiFyPath . ($path ? self::DS . ltrim($path, self::DS) : $path), $rel);
    }

    /**
     * @inheritdoc
     */
    public function isWpClassic(): bool
    {
        return rtrim(ABSPATH, self::DS) === $this->getBasePath();
    }

    /**
     * @inheritdoc
     */
    public function makeRelativePath(string $target_path, ?string $base_path = null): string
    {
        $base_path = $base_path ? : $this->getBasePath();

        $path = (new SfFilesystem())->makePathRelative($target_path, $base_path);

        return !is_dir($target_path) ? rtrim($path, self::DS) : $path;
    }
}