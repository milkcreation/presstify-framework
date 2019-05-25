<?php declare(strict_types=1);

namespace tiFy\Kernel;

use League\Flysystem\FilesystemNotFoundException;
use tify\Contracts\Container\Container;
use tiFy\Contracts\Filesystem\Filesystem as FilesystemContract;
use tiFy\Contracts\Kernel\Path as PathContract;
use tiFy\Filesystem\StorageManager;

class Path extends StorageManager implements PathContract
{
    /**
     * Séparateur de dossier.
     * @var string
     */
    const DS = DIRECTORY_SEPARATOR;

    /**
     * Instance du conteneur d'injection de dépendances.
     * @var Container
     */
    protected $container;

    /**
     * CONSTRUCTEUR
     *
     * @param Container $container Instance du conteneur d'injection de dépendances.
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        parent::__construct($this->container);
    }

    /**
     * @inheritDoc
     */
    public function diskBase(): FilesystemContract
    {
        if (!$disk = $this->getFilesystem('base')) {
            $disk = $this->mount('base', ROOT_PATH);
        }

        return $disk;
    }

    /**
     * @inheritDoc
     */
    public function diskCache(): FilesystemContract
    {
        if (!$disk = $this->getFilesystem('cache')) {
            $disk = $this->mount('cache', $this->getStoragePath('/cache'));
        }

        return $disk;
    }

    /**
     * @inheritDoc
     */
    public function diskConfig(): FilesystemContract
    {
        if (!$disk = $this->getFilesystem('config')) {
            $disk = $this->mount('config', !$this->isWp()
                ? $this->getBasePath('config') : get_template_directory() . '/config'
            );
        }

        return $disk;
    }

    /**
     * @inheritDoc
     */
    public function diskLog(): FilesystemContract
    {
        if (!$disk = $this->getFilesystem('log')) {
            $disk = $this->mount('log', $this->getStoragePath('/log'));
        }

        return $disk;
    }

    /**
     * @inheritDoc
     */
    public function diskPathFromBase(FilesystemContract $disk, string $path = '', bool $absolute = true): ?string
    {
        $path = preg_replace('#^' . preg_quote($this->getBasePath(), self::DS) . "#", '', $disk->path($path), 1, $n);

        return $n === 1 ? $this->getBasePath($path, $absolute) : null;
    }

    /**
     * @inheritDoc
     */
    public function diskPublic(): FilesystemContract
    {
        if (!$disk = $this->getFilesystem('public')) {
            $disk = $this->mount('public', !$this->isWp() ? $this->getBasePath('/public') : ABSPATH);
        }

        return $disk;
    }

    /**
     * @inheritDoc
     */
    public function diskStorage(): FilesystemContract
    {
        if (!$disk = $this->getFilesystem('storage')) {
            $disk = $this->mount('storage', !$this->isWp()
                ? $this->getBasePath('storage') : WP_CONTENT_DIR . '/uploads'
            );
        }

        return $disk;
    }

    /**
     * @inheritDoc
     */
    public function diskTheme(): FilesystemContract
    {
        if (!$disk = $this->getFilesystem('theme')) {
            $disk = $this->mount('theme', get_template_directory());
        }

        return $disk;
    }

    /**
     * @inheritDoc
     */
    public function diskTiFy(): FilesystemContract
    {
        if (!$disk = $this->getFilesystem('tify')) {
            $disk = $this->mount('tify', $this->getBasePath('/vendor/presstify/framework/src'));
        }

        return $disk;
    }

    /**
     * @inheritDoc
     */
    public function getBasePath(string $path = '', bool $absolute = true): string
    {
        return $this->normalize($absolute ? $this->diskBase()->path($path) : $path);
    }

    /**
     * @inheritDoc
     */
    public function getCachePath(string $path = '', bool $absolute = true): string
    {
        return $this->diskPathFromBase($this->diskCache(), $path, $absolute);
    }

    /**
     * @inheritDoc
     */
    public function getConfigPath(string $path = '', bool $absolute = true): string
    {
        return $this->diskPathFromBase($this->diskConfig(), $path, $absolute);
    }

    /**
     * {@inheritDoc}
     *
     * @return FilesystemContract
     */
    public function getFilesystem($prefix): ?FilesystemContract
    {
        try {
            /** @var FilesystemContract $filesystem */
            $filesystem = parent::getFilesystem($prefix);
            return $filesystem;
        } catch (FilesystemNotFoundException $e) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getLogPath(string $path = '', bool $absolute = true): string
    {
        return $this->diskPathFromBase($this->diskLog(), $path, $absolute);
    }

    /**
     * @inheritDoc
     */
    public function getPublicPath(string $path = '', bool $absolute = true): string
    {
        return $this->diskPathFromBase($this->diskPublic(), $path, $absolute);
    }

    /**
     * @inheritDoc
     */
    public function getStoragePath(string $path = '', bool $absolute = true): string
    {
        return $this->diskPathFromBase($this->diskStorage(), $path, $absolute);
    }

    /**
     * @inheritDoc
     */
    public function getThemePath(string $path = '', bool $absolute = true): string
    {
        return $this->diskPathFromBase($this->diskTheme(), $path, $absolute);
    }

    /**
     * @inheritDoc
     */
    public function getTifyPath(string $path = '', bool $absolute = true): string
    {
        return $this->diskPathFromBase($this->diskTiFy(), $path, $absolute);
    }

    /**
     * @inheritDoc
     */
    public function isWp(): bool
    {
        return defined('ABSPATH') && ($this->normalize(ABSPATH) === $this->normalize($this->getBasePath()));
    }

    /**
     * @inheritDoc
     */
    public function mount(string $name, string $root, array $config = []): FilesystemContract
    {
        $filesystem = $this->createLocal($root, $config);
        $this->set($name, $filesystem);

        return $filesystem;
    }

    /**
     * @inheritDoc
     */
    public function normalize($path): string
    {
        return self::DS . ltrim(rtrim($path, self::DS), self::DS);
    }

    /**
     * @inheritDoc
     */
    public function relPathFromBase(string $pathname): ?string
    {
        $path = preg_replace('#^' . preg_quote($this->getBasePath(), self::DS) . "#", '', $pathname, 1, $n);

        return $n === 1 ? $this->getBasePath($path): null;
    }
}