<?php declare(strict_types=1);

namespace tiFy\Template\Templates\FileManager;

use Pollen\Event\TriggeredEventInterface;
use tiFy\Contracts\Filesystem\Filesystem as BaseFilesystem;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Template\Factory\ServiceProvider as BaseServiceProvider;
use tiFy\Template\Templates\FileManager\Contracts\{
    Actions as ActionsContract,
    Ajax as AjaxContract,
    Breadcrumb,
    Cache,
    Factory,
    FileCollection,
    HttpController,
    HttpXhrController,
    IconSet,
    FileInfo,
    Filesystem,
    FileTag,
    Params,
    Sidebar
};
use tiFy\Contracts\View\Engine as ViewEngine;
use tiFy\Support\Proxy\View as ProxyView;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Instance du gabarit d'affichage.
     * @var Factory
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        parent::boot();

        events()->listen(
            'template.factory.prepared',
            function (TriggeredEventInterface $event, string $name) {
                if ($name === $this->factory->name()) {
                    $this->factory->ajax()->parse();
                }
            }
        );
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
        $this->registerFactoryParams();
        $this->registerFactorySidebar();
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryActions(): void
    {
        $this->getContainer()->share(
            $this->getFactoryAlias('actions'),
            function (): ActionsContract {
                $ctrl = $this->factory->provider('actions');

                $ctrl = $ctrl instanceof ActionsContract
                    ? $ctrl
                    : $this->getContainer()->get(ActionsContract::class);

                return $ctrl->setTemplateFactory($this->factory);
            }
        );
    }

    /**
     * Déclaration du controleur de gestion des requêtes ajax.
     *
     * @return void
     */
    public function registerFactoryAjax(): void
    {
        $this->getContainer()->share(
            $this->getFactoryAlias('ajax'),
            function () {
                $ctrl = $this->factory->provider('ajax');
                $ctrl = $ctrl instanceof AjaxContract
                    ? $ctrl
                    : $this->getContainer()->get(AjaxContract::class);

                $attrs = $this->factory->param('ajax', []);
                if (is_string($attrs)) {
                    $attrs = [
                        'url'      => $attrs,
                        'dataType' => 'json',
                        'type'     => 'POST',
                    ];
                }

                return $ctrl->setTemplateFactory($this->factory)->set(is_array($attrs) ? $attrs : []);
            }
        );
    }

    /**
     * Déclaration du controleur de fil d'ariane.
     *
     * @return void
     */
    public function registerFactoryBreadcrumb(): void
    {
        $this->getContainer()->add(
            $this->getFactoryAlias('breadcrumb'),
            function () {
                $ctrl = $this->factory->provider('breadcrumb');
                $ctrl = $ctrl instanceof Breadcrumb
                    ? clone $ctrl
                    : $this->getContainer()->get(Breadcrumb::class);

                return $ctrl->setTemplateFactory($this->factory)->setPath();
            }
        );
    }

    /**
     * Déclaration du controleur de cache.
     *
     * @return void
     */
    public function registerFactoryCache(): void
    {
        $this->getContainer()->add(
            $this->getFactoryAlias('cache'),
            function () {
                $ctrl = $this->factory->provider('cache');

                $ctrl = $ctrl instanceof Cache
                    ? clone $ctrl
                    : $this->getContainer()->get(Cache::class);

                $root = paths()->getCachePath('Template/' . $this->factory->name());
                $repo = $this->getContainer()->get(LocalFilesystem::class, [$root]);

                return $ctrl->setTemplateFactory($this->factory)
                    ->setSource($this->factory->filesystem())->setCache($repo);
            }
        );
    }

    /**
     * Déclaration du controleur de gestion de liste de fichiers.
     *
     * @return void
     */
    public function registerFactoryFileCollection(): void
    {
        $this->getContainer()->add(
            $this->getFactoryAlias('file-collection'),
            function (array $files = []) {
                $ctrl = $this->factory->provider('file-collection');
                $ctrl = $ctrl instanceof FileCollection
                    ? clone $ctrl
                    : $this->getContainer()->get(FileCollection::class);

                return $ctrl->setTemplateFactory($this->factory)->set($files);
            }
        );
    }

    /**
     * Déclaration du controleur d'informations fichier.
     *
     * @return void
     */
    public function registerFactoryFileInfo(): void
    {
        $this->getContainer()->add(
            $this->getFactoryAlias('file-info'),
            function (array $infos) {
                $ctrl = $this->factory->provider('file-info');
                $ctrl = $ctrl instanceof FileInfo
                    ? clone $ctrl
                    : $this->getContainer()->get(FileInfo::class, [$infos]);

                return $ctrl->setTemplateFactory($this->factory);
            }
        );
    }

    /**
     * Déclaration du controleur de système de fichiers.
     *
     * @return void
     */
    public function registerFactoryFilesystem(): void
    {
        $this->getContainer()->share(
            $this->getFactoryAlias('filesystem'),
            function () {
                $ctrl = $this->factory->provider('filesystem');

                return $ctrl instanceof BaseFilesystem
                    ? $ctrl
                    : $this->getContainer()->get(Filesystem::class, [$this->factory]);
            }
        );
    }

    /**
     * Déclaration du controleur de mots clefs d'un fichier.
     *
     * @return void
     */
    public function registerFactoryFileTag(): void
    {
        $this->getContainer()->add(
            $this->getFactoryAlias('file-tag'),
            function (FileInfo $file) {
                $ctrl = $this->factory->provider('file-tag');
                $ctrl = $ctrl instanceof FileTag ? clone $ctrl : $this->getContainer()->get(FileTag::class);

                return $ctrl->setTemplateFactory($this->factory)->setFile($file);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryHttpController(): void
    {
        $this->getContainer()->add(
            $this->getFactoryAlias('controller'),
            function () {
                $ctrl = $this->factory->provider('controller');
                $ctrl = $ctrl instanceof HttpController
                    ? clone $ctrl
                    : $this->getContainer()->get(HttpController::class);

                return $ctrl->setTemplateFactory($this->factory);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryHttpXhrController(): void
    {
        $this->getContainer()->add(
            $this->getFactoryAlias('xhr'),
            function () {
                $ctrl = $this->factory->provider('xhr');
                $ctrl = $ctrl instanceof HttpXhrController
                    ? clone $ctrl
                    : $this->getContainer()->get(HttpXhrController::class);

                return $ctrl->setTemplateFactory($this->factory);
            }
        );
    }

    /**
     * Déclaration du controleur de gestion des icones.
     *
     * @return void
     */
    public function registerFactoryIconSet(): void
    {
        $this->getContainer()->share(
            $this->getFactoryAlias('icon-set'),
            function () {
                $ctrl = $this->factory->provider('icon-set');
                $ctrl = $ctrl instanceof IconSet
                    ? $ctrl
                    : $this->getContainer()->get(IconSet::class);

                return $ctrl->setTemplateFactory($this->factory)->set($this->factory->param('icon', []))->parse();
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryParams(): void
    {
        $this->getContainer()->share(
            $this->getFactoryAlias('params'),
            function () {
                $ctrl = $this->factory->provider('params');
                $ctrl = $ctrl instanceof Params
                    ? $ctrl
                    : $this->getContainer()->get(Params::class);

                $attrs = $this->factory->get('params', []);

                return $ctrl->setTemplateFactory($this->factory)->set(is_array($attrs) ? $attrs : [])->parse();
            }
        );
    }

    /**
     * Déclaration du controleur de barre latérale de contrôle.
     *
     * @return void
     */
    public function registerFactorySidebar(): void
    {
        $this->getContainer()->add(
            $this->getFactoryAlias('sidebar'),
            function () {
                $ctrl = $this->factory->provider('sidebar');
                $ctrl = $ctrl instanceof Sidebar
                    ? clone $ctrl
                    : $this->getContainer()->get(Sidebar::class);

                return $ctrl->setTemplateFactory($this->factory);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function registerFactoryViewer(): void
    {
        $this->getContainer()->share(
            $this->getFactoryAlias('viewer'),
            function () {
                $params = $this->factory->get('viewer', []);

                if (!$params instanceof ViewEngine) {
                    $viewer = ProxyView::getPlatesEngine(
                        array_merge(
                            [
                                'directory' => template()->resourcesDir('/views/file-manager'),
                                'factory'   => View::class,
                            ],
                            (array)$params
                        )
                    );
                } else {
                    $viewer = $params;
                }

                $viewer->params(['template' => $this->factory]);

                return $viewer;
            }
        );
    }
}