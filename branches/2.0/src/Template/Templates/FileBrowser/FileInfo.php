<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileBrowser;

use BadMethodCallException;
use Exception;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Adapter\Local;
use SplFileInfo;
use Mimey\MimeTypes;
use tiFy\Contracts\Filesystem\Filesystem;
use tiFy\Contracts\Template\TemplateFactory;
use tiFy\Support\ParamsBag;
use tiFy\Support\DateTime;
use tiFy\Template\Factory\FactoryAwareTrait;
use tiFy\Template\Templates\FileBrowser\Contracts\{FileBrowser, FileInfo as FileInfoContract};

/**
 * @mixin SplFileInfo
 */
class FileInfo extends ParamsBag implements FileInfoContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit d'affichage.
     * @var FileBrowser
     */
    protected $factory;

    /**
     * Instance des informations de fichiers locaux.
     * @return SplFileInfo|false
     */
    protected $splFileInfo;

    /**
     * CONSTRUCTEUR
     *
     * @param array $infos Liste des informations associé au fichier.
     *
     * @return void
     */
    public function __construct(array $infos)
    {
        $this->set($infos)->parse();
    }

    /**
     * @inheritDoc
     */
    public function __call(string $name, array $arguments)
    {
        if (is_null($this->splFileInfo)) {
            $this->splFileInfo = !$this->isLocal() ? false : new SplFileInfo($this->getPathname());
        }

        if ($this->splFileInfo instanceof SplFileInfo) {
            try {
                return $this->splFileInfo->$name(...$arguments);
            } catch (Exception $e) {
                throw new BadMethodCallException(sprintf(__('La méthode %s n\'est pas disponible.', 'tify'), $name));
            }
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return (string)$this->factory->viewer('file-infos', ['file' => $this]);
    }

    /**
     * Récupération de l'instance du gestionnaire de contexte de fichiers.
     *
     * @return AdapterInterface
     */
    protected function adapter(): AdapterInterface
    {
        return $this->filesystem()->getAdapter();
    }

    /**
     * Récupération de l'instance d'une date.
     *
     * @param int $timestamp Date au format Unix.
     *
     * @return DateTime|null
     */
    protected function datetime(int $timestamp): ?DateTime
    {
        try {
            return (new DateTime())->setTimestamp($timestamp);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Récupération de l'instance du gestionnaire de fichiers.
     *
     * @return Filesystem
     */
    protected function filesystem(): Filesystem
    {
        return $this->factory->filesystem();
    }

    /**
     * @inheritDoc
     */
    public function getBasename(): string
    {
        return $this->get('basename');
    }

    /**
     * @inheritDoc
     */
    public function getDirname(): string
    {
        return $this->get('dirname');
    }

    /**
     * @inheritDoc
     */
    public function getDownloadUrl(): string
    {
        return $this->getFactory()->baseUrl() . '?dl=' . $this->getRelPath();
    }

    /**
     * @inheritDoc
     */
    public function getExtension(): string
    {
        return $this->get('extension');
    }

    /**
     * {@inheritDoc}
     *
     * @return FileBrowser
     */
    public function getFactory(): TemplateFactory
    {
        return $this->factory;
    }

    /**
     * @inheritDoc
     */
    public function getHumanDate(string $format = 'Y-m-d'): ?string
    {
        return ($datetime = $this->datetime($this->getTimestamp())) ? $datetime->format($format) : null;
    }

    /**
     * @inheritDoc
     */
    public function getHumanSize(int $decimals = 2): ?string
    {
        if ($bytes = $this->getSize()) {
            $sz = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            $factor = floor((strlen((string)$bytes) - 1) / 3);

            return sprintf("%.{$decimals}f %s", $bytes / pow(1024, $factor), ($sz[(int)$factor] ?? ''));
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getHumanType(): ?string
    {
        switch($this->getType()) {
            case 'dir' :
                return __('Répertoire', 'tify');
                break;
            case 'file' :
                return __('Fichier', 'tify');
                break;
            case 'link' :
                return __('Lien symbolique', 'tify');
                break;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): string
    {
        return $this->getFactory()->getIcon('file', $this);
    }

    /**
     * @inheritDoc
     */
    public function getMimetype(): ?string
    {
        return $this->isFile() ? (new MimeTypes())->getMimeType($this->getExtension()) : 'directory';
    }

    /**
     * @inheritDoc
     */
    public function getPathname(): string
    {
        if ($this->isLocal()) {
            /** @var Local $adapter */
            $adapter = $this->adapter();

            return $adapter->applyPathPrefix($this->getRelPath());
        }

        return $this->getRelPath();
    }

    /**
     * @inheritDoc
     */
    public function getRelPath(): string
    {
        return $this->get('path');
    }

    /**
     * @inheritDoc
     */
    public function getSize(): float
    {
        return $this->get('size') ? : 0;
    }

    /**
     * @inheritDoc
     */
    public function getTimestamp(): int
    {
        return $this->get('timestamp');
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->get('type');
    }

    /**
     * @inheritDoc
     */
    public function isDir(): bool
    {
        return $this->getType() === 'dir';
    }

    /**
     * @inheritDoc
     */
    public function isFile(): bool
    {
        return $this->getType() === 'file';
    }

    /**
     * @inheritDoc
     */
    public function isLink(): bool
    {
        return $this->getType() === 'link';
    }

    /**
     * @inheritDoc
     */
    public function isLocal(): bool
    {
        return $this->adapter() instanceof Local;
    }

    /**
     * @inheritDoc
     */
    public function isRoot(): bool
    {
        return ! $this->getRelPath() || ($this->getRelPath() === '/');
    }

    /**
     * @inheritDoc
     */
    public function isSelected(): bool
    {
        return $this->getRelPath() === $this->getFactory()->getPath();
    }
}