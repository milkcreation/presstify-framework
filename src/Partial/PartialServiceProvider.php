<?php declare(strict_types=1);

namespace tiFy\Partial;

use tiFy\Container\ServiceProvider;
use tiFy\Contracts\Partial\{
    Accordion as AccordionContract,
    Breadcrumb as BreadcrumbContract,
    CookieNotice as CookieNoticeContract,
    CurtainMenu as CurtainMenuContract,
    Dropdown as DropdownContract,
    Downloader as DownloaderContract,
    Holder as HolderContract,
    ImageLightbox as ImageLightboxContract,
    Modal as ModalContract,
    Notice as NoticeContract,
    Pagination as PaginationContract,
    Partial as PartialContract,
    PartialFactory,
    Pdfviewer as PdfviewerContract,
    Progress as ProgressContract,
    Sidebar as SidebarContract,
    Slider as SliderContract,
    Spinner as SpinnerContract,
    Tab as TabContract,
    Table as TableContract,
    Tag as TagContract
};
use tiFy\Partial\Partials\{
    Accordion\Accordion,
    Breadcrumb\Breadcrumb,
    CookieNotice\CookieNotice,
    CurtainMenu\CurtainMenu,
    Dropdown\Dropdown,
    Downloader\Downloader,
    Holder\Holder,
    ImageLightbox\ImageLightbox,
    Modal\Modal,
    Notice\Notice,
    Pagination\Pagination,
    Pdfviewer\Pdfviewer,
    Progress\Progress,
    Sidebar\Sidebar,
    Slider\Slider,
    Spinner\Spinner,
    Tab\Tab,
    Table\Table,
    Tag\Tag};

class PartialServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        'partial',
        'partial.viewer',
        AccordionContract::class,
        BreadcrumbContract::class,
        CookieNoticeContract::class,
        CurtainMenuContract::class,
        DropdownContract::class,
        HolderContract::class,
        ImageLightboxContract::class,
        ModalContract::class,
        NoticeContract::class,
        PaginationContract::class,
        PdfviewerContract::class,
        ProgressContract::class,
        SidebarContract::class,
        SliderContract::class,
        SpinnerContract::class,
        TabContract::class,
        TableContract::class,
        TagContract::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('partial', function () {
            return new Partial($this->getContainer());
        });

        $this->registerFactories();

        $this->registerViewer();
    }

    /**
     * Déclaration des controleurs de portions d'affichage.
     *
     * @return void
     */
    public function registerFactories(): void
    {
        $this->getContainer()->add(AccordionContract::class, function () {
            return new Accordion();
        });

        $this->getContainer()->add(BreadcrumbContract::class, function () {
            return new Breadcrumb();
        });

        $this->getContainer()->add(CookieNoticeContract::class, function () {
            return new CookieNotice();
        });

        $this->getContainer()->add(CurtainMenuContract::class, function () {
            return new CurtainMenu();
        });

        $this->getContainer()->add(DropdownContract::class, function () {
            return new Dropdown();
        });

        $this->getContainer()->add(DownloaderContract::class, function () {
            return new Downloader();
        });

        $this->getContainer()->add(HolderContract::class, function () {
            return new Holder();
        });

        $this->getContainer()->add(ImageLightboxContract::class, function () {
            return new ImageLightbox();
        });

        $this->getContainer()->add(ModalContract::class, function () {
            return new Modal();
        });

        $this->getContainer()->add(NoticeContract::class, function () {
            return new Notice();
        });

        $this->getContainer()->add(PaginationContract::class, function () {
            return new Pagination();
        });

        $this->getContainer()->add(PdfviewerContract::class, function () {
            return new Pdfviewer();
        });

        $this->getContainer()->add(ProgressContract::class, function () {
            return new Progress();
        });

        $this->getContainer()->add(SidebarContract::class, function () {
            return new Sidebar();
        });

        $this->getContainer()->add(SliderContract::class, function () {
            return new Slider();
        });

        $this->getContainer()->add(SpinnerContract::class, function () {
            return new Spinner();
        });

        $this->getContainer()->add(TabContract::class, function () {
            return new Tab();
        });

        $this->getContainer()->add(TableContract::class, function () {
            return new Table();
        });

        $this->getContainer()->add(TagContract::class, function () {
            return new Tag();
        });
    }

    /**
     * Déclaration du controleur d'affichage.
     *
     * @return void
     */
    public function registerViewer(): void
    {
        $this->getContainer()->add('partial.viewer', function (PartialFactory $factory) {
            /** @var PartialContract $manager */
            $manager = $this->getContainer()->get('partial');
            $alias = $factory->getAlias();

            $directory = $factory->get(
                'viewer.directory',
                config('partial.viewer.directory', $manager->resourcesDir('/views')) . "/{$alias}"
            );

            $override_dir = $factory->get(
                'viewer.override_dir',
                ($dir = config('partial.viewer.override_dir')) ? "{$dir}/{$alias}" : ''
            );

            return view()
                ->setDirectory(is_dir($directory) ? $directory : null)
                ->setController(PartialView::class)
                ->setOverrideDir((($override_dir) && is_dir($override_dir))
                    ? $override_dir
                    : (is_dir($directory) ? $directory : __DIR__)
                )
                ->setParam('partial', $factory);
        });
    }
}