<?php declare(strict_types=1);

namespace tiFy\Filesystem;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use tiFy\Contracts\Container\Container;
use tiFy\Contracts\Filesystem\Filesystem;
use tiFy\Contracts\Filesystem\StaticCacheManager as StaticCacheManagerContract;

class StaticCacheManager extends StorageManager implements StaticCacheManagerContract
{
    /**
     * CONSTRUCTEUR.
     *
     * @param Container $container Instance du conteneur d'injection de dépendances.
     * @param string| $cache_dir Chemin relatif vers le répertoire de stockage du cache.
     *
     * @return void
     */
    public function __construct(Container $container, ?string $cache_dir = null)
    {
        parent::__construct($container);

        if ($cache_dir) {
            $path = ROOT_PATH . $cache_dir;
            $this->setCache($this->createLocal($path));
        }
    }

    /**
     * @inheritDoc
     */
    public function getResponse(string $path, ServerRequestInterface $psrRequest): StreamedResponse
    {
        $path = rawurldecode($path);

        if (!$this->getCache()->has($path)) {
            $this->put('cache://'. $path, $this->read('source://'. $path));
        }

        return $this->getCache()->response($path);
    }

    /**
     * @inheritDoc
     */
    public function ready(): bool
    {
        return $this->getCache() && $this->getSource();
    }

    /**
     * @inheritDoc
     */
    public function getCache(): ?Filesystem
    {
        return $this->disk('cache');
    }

    /**
     * @inheritDoc
     */
    public function getSource(): ?Filesystem
    {
        return $this->disk('source');
    }

    /**
     * @inheritDoc
     */
    public function setCache(Filesystem $cache): StaticCacheManagerContract
    {
        $this->mountFilesystem('cache', $cache);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSource(Filesystem $source): StaticCacheManagerContract
    {
        $this->mountFilesystem('source', $source);

        return $this;
    }
}