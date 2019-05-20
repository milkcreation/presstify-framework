<?php declare(strict_types=1);

namespace tiFy\Template;

use tiFy\Container\ServiceProvider;
use tiFy\Contracts\Template\TemplateFactory as TemplateFactoryContract;
use tiFy\Template\Templates\FileBrowser\Contracts\{
    Ajax as AjaxContract,
    Breadcrumb as BreadcrumbContract,
    FileBrowser as FileBrowserContract,
    FileCollection as FileCollectionContract,
    IconSet as IconSetContract,
    FileInfo as FileInfoContract,
    Filesystem as FilesystemContract,
    Sidebar as SidebarContract
};
use tiFy\Template\Templates\FileBrowser\{Ajax, Breadcrumb, FileCollection, IconSet, FileInfo, Filesystem, Sidebar};

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
        // FileBrowser
        AjaxContract::class,
        BreadcrumbContract::class,
        FileCollectionContract::class,
        FileInfoContract::class,
        FilesystemContract::class,
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

        $this->registerFileBrowser();
    }

    /**
     * @inheritDoc
     */
    public function registerFileBrowser(): void
    {
        $this->getContainer()->add(AjaxContract::class, function () {
            return new Ajax();
        });

        $this->getContainer()->add(BreadcrumbContract::class, function () {
            return new Breadcrumb();
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

        $this->getContainer()->add(FilesystemContract::class, function (FileBrowserContract $factory) {
            return Filesystem::createFromFactory($factory);
        });

        $this->getContainer()->add(SidebarContract::class, function () {
            return new Sidebar();
        });
    }
}