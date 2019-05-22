<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileManager;

use League\Flysystem\Adapter\Local;
use tiFy\Contracts\Filesystem\Filesystem as tiFyFilesystem;
use tiFy\Template\Factory\FactoryServiceProvider;
use tiFy\Template\Templates\FileManager\Contracts\{
    Ajax as AjaxContract,
    Breadcrumb,
    Cache,
    FileManager,
    FileCollection,
    HttpController,
    HttpXhrController,
    IconSet,
    FileInfo,
    Filesystem,
    FileTag,
    Sidebar};
use tiFy\View\ViewEngine;

class FileManagerServiceProvider extends FactoryServiceProvider
{
    /**
     * Instance du gabarit d'affichage.
     * @var FileManager
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        parent::boot();

        events()->listen('template.factory.prepare', function (string $name) {
            if ($name === $this->factory->name()) {
                $this->factory->ajax()->parse();
            }
        });
    }

    /**
     * @inheritDoc
     */
    public function registerFactories(): void
    {
        parent::registerFactories();

        $this->registerFactoryAjax();
        $this->registerFactoryBreadcrumb();
        $this->registerFactoryCache();
        $this->registerFactoryFileCollection();
        $this->registerFactoryFileInfo();
        $this->registerFactoryFilesystem();
        $this->registerFactoryFileTag();
        $this->registerFactoryHttpController();
        $this->registerFactoryHttpXhrController();
        $this->registerFactoryIconSet();
        $this->registerFactorySidebar();
    }

    /**
     * Déclaration du controleur de gestion des requêtes ajax.
     *
     * @return void
     */
    public function registerFactoryAjax(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('ajax'), function () {
            $ajax = $this->factory->get('providers.ajax');
            $ajax = $ajax instanceof AjaxContract
                ? $ajax
                : $this->getContainer()->get(AjaxContract::class);

            $attrs = $this->factory->param('ajax', []);
            if (is_string($attrs)) {
                $attrs = [
                    'url'      => $attrs,
                    'dataType' => 'json',
                    'type'     => 'POST'
                ];
            }

            return $ajax->setFactory($this->factory)->set(is_array($attrs) ? $attrs : []);
        });
    }

    /**
     * Déclaration du controleur de fil d'ariane.
     *
     * @return void
     */
    public function registerFactoryBreadcrumb(): void
    {
        $this->getContainer()->add($this->getFactoryAlias('breadcrumb'), function () {
            $breadcrumb = $this->factory->get('providers.breadcrumb');
            $breadcrumb = $breadcrumb instanceof Breadcrumb
                ? $breadcrumb
                : $this->getContainer()->get(Breadcrumb::class);

            return $breadcrumb->setFactory($this->factory)->setPath();
        });
    }

    /**
     * Déclaration du controleur de cache.
     *
     * @return void
     */
    public function registerFactoryCache(): void
    {
        $this->getContainer()->add($this->getFactoryAlias('cache'), function () {
            $cache = $this->factory->get('providers.cache');
            $cache = $cache instanceof Cache
                ? $cache
                : $this->getContainer()->get(Cache::class);

            $root = paths()->getCachePath('Template/' . $this->factory->name());
            $repo = $this->getContainer()->get(tiFyFilesystem::class, [new Local($root)]);

            return $cache->setFactory($this->factory)
                ->setSource($this->factory->filesystem())->setCache($repo);
        });
    }

    /**
     * Déclaration du controleur de gestion de liste de fichiers.
     *
     * @return void
     */
    public function registerFactoryFileCollection(): void
    {
        $this->getContainer()->add($this->getFactoryAlias('file-collection'), function (array $files = []) {
            $fileCollect = $this->factory->get('providers.file-collection');
            $fileCollect = $fileCollect instanceof FileCollection
                ? $fileCollect
                : $this->getContainer()->get(FileCollection::class);

            return $fileCollect->setFactory($this->factory)->set($files);
        });
    }

    /**
     * Déclaration du controleur d'informations fichier.
     *
     * @return void
     */
    public function registerFactoryFileInfo(): void
    {
        $this->getContainer()->add($this->getFactoryAlias('file-info'), function (array $infos) {
            $fileInfo = $this->factory->get('providers.file-info');

            $fileInfo = $fileInfo instanceof FileInfo
                ? $fileInfo
                : $this->getContainer()->get(FileInfo::class, [$infos]);

            return $fileInfo->setFactory($this->factory);
        });
    }

    /**
     * Déclaration du controleur de système de fichiers.
     *
     * @return void
     */
    public function registerFactoryFilesystem(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('filesystem'), function () {
            $filesystem = $this->factory->get('providers.filesystem');

            return $filesystem instanceof tiFyFilesystem
                ? $filesystem
                : $this->getContainer()->get(Filesystem::class, [$this->factory]);
        });
    }

    /**
     * Déclaration du controleur de mots clefs d'un fichier.
     *
     * @return void
     */
    public function registerFactoryFileTag(): void
    {
        $this->getContainer()->add($this->getFactoryAlias('file-tag'), function (FileInfo $file) {
            $tag = $this->factory->get('providers.file-tag');

            $tag = $tag instanceof FileTag ? $tag : $this->getContainer()->get(FileTag::class);

            return $tag->setFactory($this->factory)->setFile($file);
        });
    }

    /**
     * Déclaration du controleur de requête HTTP.
     *
     * @return void
     */
    public function registerFactoryHttpController(): void
    {
        $this->getContainer()->add($this->getFactoryAlias('controller'), function () {
            $controller = $this->factory->get('providers.controller');
            $controller = $controller instanceof HttpController
                ? $controller
                : $this->getContainer()->get(HttpController::class);

            return $controller->setFactory($this->factory);
        });
    }

    /**
     * Déclaration du controleur de requête HTTP XHR.
     *
     * @return void
     */
    public function registerFactoryHttpXhrController(): void
    {
        $this->getContainer()->add($this->getFactoryAlias('xhr'), function () {
            $xhr = $this->factory->get('providers.xhr');
            $xhr = $xhr instanceof HttpXhrController
                ? $xhr
                : $this->getContainer()->get(HttpXhrController::class);

            return $xhr->setFactory($this->factory);
        });
    }

    /**
     * Déclaration du controleur de gestion des icones.
     *
     * @return void
     */
    public function registerFactoryIconSet(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('icon-set'), function () {
            $iconSet = $this->factory->get('providers.icon-set');
            $iconSet = $iconSet instanceof IconSet
                ? $iconSet
                : $this->getContainer()->get(IconSet::class);

            return $iconSet->setFactory($this->factory)->set($this->factory->param('icon', []))->parse();
        });
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryParams(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('params'), function () {
            return new Params($this->factory);
        });
    }

    /**
     * Déclaration du controleur de barre latérale de contrôle.
     *
     * @return void
     */
    public function registerFactorySidebar(): void
    {
        $this->getContainer()->add($this->getFactoryAlias('sidebar'), function () {
            $sidebar = $this->factory->get('providers.sidebar');
            $sidebar = $sidebar instanceof Sidebar
                ? $sidebar
                : $this->getContainer()->get(Sidebar::class);

            return $sidebar->setFactory($this->factory);
        });
    }

    /**
     * Déclaration du controleur de gabarit d'affichage.
     *
     * @return void
     */
    public function registerFactoryViewer(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('viewer'), function () {
            $params = $this->factory->get('viewer', []);

            if (!$params instanceof ViewEngine) {
                $viewer = new ViewEngine(array_merge([
                    'directory' => template()->resourcesDir('/views/file-manager')
                ], $params));
                $viewer->setController(Viewer::class);

                if (!$viewer->getOverrideDir()) {
                    $viewer->setOverrideDir(template()->resourcesDir('/views/file-manager'));
                }
            } else {
                $viewer = $params;
            }

            $viewer->set('factory', $this->factory);

            return $viewer;
        });
    }
}