<?php declare(strict_types=1);

namespace tiFy\Wordpress\Partial;

use Exception;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Partial\Accordion as AccordionContract;
use tiFy\Contracts\Partial\Breadcrumb as BreadcrumbContract;
use tiFy\Contracts\Partial\CookieNotice as CookieNoticeContract;
use tiFy\Contracts\Partial\CurtainMenu as CurtainMenuContract;
use tiFy\Contracts\Partial\Dropdown as DropdownContract;
use tiFy\Contracts\Partial\Downloader as DownloaderContract;
use tiFy\Contracts\Partial\Holder as HolderContract;
use tiFy\Contracts\Partial\ImageLightbox as ImageLightboxContract;
use tiFy\Contracts\Partial\Modal as ModalContract;
use tiFy\Contracts\Partial\Notice as NoticeContract;
use tiFy\Contracts\Partial\Pagination as PaginationContract;
use tiFy\Contracts\Partial\Partial as Manager;
use tiFy\Contracts\Partial\Pdfviewer as PdfviewerContract;
use tiFy\Contracts\Partial\Sidebar as SidebarContract;
use tiFy\Contracts\Partial\Slider as SliderContract;
use tiFy\Contracts\Partial\Spinner as SpinnerContract;
use tiFy\Contracts\Partial\Tab as TabContract;
use tiFy\Contracts\Partial\Table as TableContract;
use tiFy\Wordpress\Contracts\Partial\MediaLibrary as MediaLibraryContract;
use tiFy\Wordpress\Partial\Driver\Accordion\Accordion;
use tiFy\Wordpress\Partial\Driver\Breadcrumb\Breadcrumb;
use tiFy\Wordpress\Partial\Driver\CookieNotice\CookieNotice;
use tiFy\Wordpress\Partial\Driver\CurtainMenu\CurtainMenu;
use tiFy\Wordpress\Partial\Driver\Dropdown\Dropdown;
use tiFy\Wordpress\Partial\Driver\Downloader\Downloader;
use tiFy\Wordpress\Partial\Driver\Holder\Holder;
use tiFy\Wordpress\Partial\Driver\ImageLightbox\ImageLightbox;
use tiFy\Wordpress\Partial\Driver\Modal\Modal;
use tiFy\Wordpress\Partial\Driver\MediaLibrary\MediaLibrary;
use tiFy\Wordpress\Partial\Driver\Notice\Notice;
use tiFy\Wordpress\Partial\Driver\Pagination\Pagination;
use tiFy\Wordpress\Partial\Driver\Pdfviewer\Pdfviewer;
use tiFy\Wordpress\Partial\Driver\Sidebar\Sidebar;
use tiFy\Wordpress\Partial\Driver\Slider\Slider;
use tiFy\Wordpress\Partial\Driver\Spinner\Spinner;
use tiFy\Wordpress\Partial\Driver\Tab\Tab;
use tiFy\Wordpress\Partial\Driver\Table\Table;

class Partial
{
    /**
     * Instance du conteneur d'injection de dépendances.
     * @var Container
     */
    protected $container;

    /**
     * Définition des pilotes spécifiques à Wordpress.
     * @var array
     */
    protected $drivers = [
        'media-library' => MediaLibraryContract::class,
    ];

    /**
     * Instance du gestionnaire des portions d'affichage.
     * @var Manager
     */
    protected $manager;

    /**
     * @param Manager $manager Instance du gestionnaire des portions d'affichage.
     *
     * @return void
     *
     * @throws Exception
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        $this->container = $this->manager->getContainer();

        $this->registerDrivers();
        $this->registerOverride();

        $this->manager->boot();
        foreach ($this->drivers as $name => $alias) {
            $this->manager->register($name, $this->getContainer()->get($alias));
        }
    }

    /**
     * Récupération du conteneur d'injection de dépendance.
     *
     * @return Container
     */
    protected function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Déclaration des pilotes spécifiques à Wordpress.
     *
     * @return void
     */
    public function registerDrivers(): void
    {
        $this->getContainer()->add(MediaLibraryContract::class, function () {
            return new MediaLibrary();
        });
    }

    /**
     * Déclaration des controleurs de surchage des portions d'affichage.
     *
     * @return void
     */
    public function registerOverride(): void
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
    }
}