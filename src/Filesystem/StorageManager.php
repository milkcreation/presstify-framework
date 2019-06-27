<?php declare(strict_types=1);

namespace tiFy\Filesystem;

use InvalidArgumentException;
use League\Flysystem\{AdapterInterface, FilesystemInterface, MountManager};
use tiFy\Contracts\Container\Container;
use tiFy\Contracts\Filesystem\{
    Filesystem as FilesystemContract,
    LocalAdapter as LocalAdapterContract,
    LocalFilesystem as LocalFilesystemContract,
    StorageManager as StorageManagerContract};

class StorageManager extends MountManager implements StorageManagerContract
{
    /**
     * Instance du conteneur d'injection de dépendance.
     * @var Container
     */
    protected $container;

    /**
     * CONSTRUCTEUR.
     *
     * @param Container|null $container Instance du conteneur d'injection de dépendances.
     *
     * @return void
     */
    public function __construct(?Container $container = null)
    {
        $this->container = $container;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function disk(string $name): FilesystemContract
    {
        return $this->getFilesystem($name);
    }

    /**
     * @inheritDoc
     */
    public function getContainer(): ?Container
    {
        return $this->container;
    }

    /**
     * @inheritDoc
     */
    public function getFilesystem($prefix)
    {
        return parent::getFilesystem($prefix);
    }

    /**
     * @inheritDoc
     */
    public function localAdapter(...$args): AdapterInterface
    {
        return ($this->getContainer() && $this->getContainer()->has(LocalAdapterContract::class))
            ? $this->getContainer()->get(LocalAdapterContract::class, $args)
            : new LocalAdapter(...$args);
    }

    /**
     * @inheritDoc
     */
    public function localFilesytem(...$args): LocalFilesystemContract
    {
        return $this->getContainer() && $this->getContainer()->has(LocalFilesystemContract::class)
            ? $this->getContainer()->get(LocalFilesystemContract::class, $args)
            : new LocalFilesystem(...$args);
    }

    /**
     * @inheritDoc
     */
    public function mountFilesystem($name, FilesystemInterface $filesystem)
    {
        if ($filesystem instanceof FilesystemContract) {
            return parent::mountFilesystem($name, $filesystem);
        }
        throw new InvalidArgumentException(
            sprintf(
                __('Impossible de monter le disque %s. Le gestionnaire de fichiers doit une instance de %s.', 'tify'),
                $name,
                FilesystemContract::class
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function register(string $name, $attrs): StorageManagerContract
    {
        if ($attrs instanceof Filesystem) {
            $filesystem = $attrs;
        } elseif (is_array($attrs)) {
            $filesystem = $this->localFilesytem($attrs['root']?? '', $attrs);
        } elseif (is_string($attrs)) {
            $filesystem = $this->localFilesytem($attrs);
        } else {
            throw new InvalidArgumentException(
                __('Les arguments ne permettent pas de définir le système de fichiers', 'theme')
            );
        }

        return $this->set($name, $filesystem);
    }

    /**
     * @inheritDoc
     */
    public function set(string $name, FilesystemContract $filesystem): StorageManagerContract
    {
        return $this->mountFilesystem($name, $filesystem);
    }
}