<?php declare(strict_types=1);

namespace tiFy\Template\Factory;

use Psr\Http\Message\ServerRequestInterface;
use League\Flysystem\MountManager;
use SplFileInfo;
use tiFy\Contracts\Template\FactoryCache as FactoryCacheContract;
use tiFy\Contracts\Filesystem\Filesystem;

class FactoryCache extends MountManager implements FactoryCacheContract
{
    use FactoryAwareTrait;

    /**
     * @inheritDoc
     */
    public function getResponse(string $path, ServerRequestInterface $psrRequest)
    {
        $path = rawurldecode($path);

        if (!$this->getCache()->has($path)) {
            $this->put('cache://'. $path, $this->read('source://'. $path));
        }

        return $this->getCache()->response($path);
    }

    /**
     * Vérifie si l'instance est opérationnelle.
     *
     * @return boolean
     */
    public function ready(): bool
    {
        /*if (($this->cache instanceof Filesystem) && ($this->source instanceof Filesystem)) {
            return true;
        }*/
        return true;
    }

    /**
     * Récupération du gestionnaire des ressources en cache.
     *
     * @return Filesystem
     */
    public function getCache(): Filesystem
    {
        return $this->getFilesystem('cache');
    }

    /**
     * Récupération du gestionnaire des ressources originales.
     *
     * @return Filesystem
     */
    public function getSource(): Filesystem
    {
        return $this->getFilesystem('source');
    }

    /**
     * Récupération du chemin de la source.
     *
     * @param string $path Chemin
     *
     * @return SplFileInfo
     */
    public function getSourceFileInfo($path): SplFileInfo
    {
        return new SplFileInfo($this->getSource()->path($path));
    }

    /**
     * Définition du gestionnaire des ressources en cache.
     *
     * @param Filesystem $cache
     *
     * @return $this
     */
    public function setCache(Filesystem $cache): FactoryCacheContract
    {
        $this->mountFilesystem('cache', $cache);

        return $this;
    }

    /**
     * Définition du gestionnaire des ressources originales.
     *
     * @param Filesystem $source
     *
     * @return $this
     */
    public function setSource(Filesystem $source): FactoryCacheContract
    {
        $this->mountFilesystem('source', $source);

        return $this;
    }
}