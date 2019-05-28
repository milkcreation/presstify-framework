<?php declare(strict_types=1);

namespace tiFy\Template;

use tiFy\Container\ServiceProvider;
use tiFy\Contracts\Template\{
    FactoryCache as FactoryCacheContract,
    FactoryHttpController as FactoryHttpControllerContract,
    FactoryHttpXhrController as FactoryHttpXhrControllerContract,
    TemplateFactory as TemplateFactoryContract
};
use tiFy\Template\Templates\FileManager\Contracts\{
    Ajax as AjaxContract,
    Breadcrumb as BreadcrumbContract,
    Cache as CacheContract,
    FileManager as FileManagerContract,
    FileCollection as FileCollectionContract,
    HttpController as HttpControllerContract,
    FileInfo as FileInfoContract,
    Filesystem as FilesystemContract,
    FileTag as FileTagContract,
    HttpXhrController as HttpXhrControllerContract,
    IconSet as IconSetContract,
    Sidebar as SidebarContract
};
use tiFy\Template\Factory\{FactoryCache, FactoryHttpController, FactoryHttpXhrController};
use tiFy\Template\Templates\FileManager\{
    Ajax,
    Breadcrumb,
    Cache,
    FileCollection,
    IconSet,
    FileInfo,
    Filesystem,
    FileTag,
    HttpController,
    HttpXhrController,
    Sidebar};

class TemplateServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'template',
        TemplateFactoryContract::class,
        // Factory
        FactoryCache::class,
        FactoryHttpControllerContract::class,
        FactoryHttpXhrControllerContract::class,
        // FileManager
        AjaxContract::class,
        BreadcrumbContract::class,
        CacheContract::class,
        FileCollectionContract::class,
        FileInfoContract::class,
        FilesystemContract::class,
        FileTagContract::class,
        HttpControllerContract::class,
        HttpXhrControllerContract::class,
        IconSetContract::class,
        SidebarContract::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('template', function () {
            return new TemplateManager($this->getContainer());
        });

        $this->getContainer()->add(TemplateFactoryContract::class, function () {
            return new TemplateFactory();
        });

        $this->registerFactories();
        $this->registerFileManager();
    }

    /**
     * @inheritDoc
     */
    public function registerFactories(): void
    {
        $this->getContainer()->add(FactoryCacheContract::class, function () {
            return new FactoryCache();
        });

        $this->getContainer()->add(FactoryHttpControllerContract::class, function () {
            return new FactoryHttpController();
        });

        $this->getContainer()->add(FactoryHttpXhrControllerContract::class, function () {
            return new FactoryHttpXhrController();
        });
    }

    /**
     * @inheritDoc
     */
    public function registerFileManager(): void
    {
        $this->getContainer()->add(AjaxContract::class, function () {
            return new Ajax();
        });

        $this->getContainer()->add(BreadcrumbContract::class, function () {
            return new Breadcrumb();
        });

        $this->getContainer()->add(CacheContract::class, function () {
            return new Cache();
        });

        $this->getContainer()->add(FileCollectionContract::class, function () {
            return new FileCollection();
        });

        $this->getContainer()->add(IconSetContract::class, function () {
            return new IconSet();
        });

        $this->getContainer()->add(FileInfoContract::class, function (array $infos) {
            return new FileInfo($infos);
        });

        $this->getContainer()->add(FilesystemContract::class, function (FileManagerContract $factory) {
            return Filesystem::createFromFactory($factory);
        });

        $this->getContainer()->add(FileTagContract::class, function () {
            return new FileTag();
        });

        $this->getContainer()->add(HttpControllerContract::class, function () {
            return new HttpController();
        });

        $this->getContainer()->add(HttpXhrControllerContract::class, function () {
            return new HttpXhrController();
        });

        $this->getContainer()->add(SidebarContract::class, function () {
            return new Sidebar();
        });
    }
}