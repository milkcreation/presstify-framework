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
use tiFy\Contracts\Partial\Partial as PartialManager;
use tiFy\Contracts\Partial\Pdfviewer as PdfviewerContract;
use tiFy\Contracts\Partial\Sidebar as SidebarContract;
use tiFy\Contracts\Partial\Slider as SliderContract;
use tiFy\Contracts\Partial\Spinner as SpinnerContract;
use tiFy\Contracts\Partial\Tab as TabContract;
use tiFy\Contracts\Partial\Table as TableContract;
use tiFy\Support\Concerns\ContainerAwareTrait;
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
    use ContainerAwareTrait;

    /**
     * Définition des pilotes spécifiques à Wordpress.
     * @var array
     */
    protected $drivers = [
        'media-library' => MediaLibraryContract::class,
    ];

    /**
     * Instance du gestionnaire des portions d'affichage.
     * @var PartialManager
     */
    protected $partialManager;

    /**
     * @param PartialManager $partialManager
     * @param Container $container
     */
    public function __construct(PartialManager $partialManager, Container $container)
    {
        $this->partialManager = $partialManager;
        $this->setContainer($container);

        $this->registerDrivers();
        $this->registerOverride();

        $this->partialManager->boot();
        foreach ($this->drivers as $name => $alias) {
            $this->partialManager->register($name, $alias);
        }
    }

    /**
     * Déclaration des pilotes spécifiques à Wordpress.
     *
     * @return void
     */
    public function registerDrivers(): void
    {
        $this->getContainer()->add(MediaLibraryContract::class, function (): MediaLibraryContract {
            return new MediaLibrary($this->partialManager);
        });
    }

    /**
     * Déclaration des controleurs de surchage des portions d'affichage.
     *
     * @return void
     */
    public function registerOverride(): void
    {
        $this->getContainer()->add(AccordionContract::class, function (): AccordionContract {
            return new Accordion($this->partialManager);
        });

        $this->getContainer()->add(BreadcrumbContract::class, function (): BreadcrumbContract {
            return new Breadcrumb($this->partialManager);
        });

        $this->getContainer()->add(CookieNoticeContract::class, function (): CookieNoticeContract {
            return new CookieNotice($this->partialManager);
        });

        $this->getContainer()->add(CurtainMenuContract::class, function (): CurtainMenuContract {
            return new CurtainMenu($this->partialManager);
        });

        $this->getContainer()->add(DropdownContract::class, function (): DropdownContract {
            return new Dropdown($this->partialManager);
        });

        $this->getContainer()->add(DownloaderContract::class, function (): DownloaderContract {
            return new Downloader($this->partialManager);
        });

        $this->getContainer()->add(HolderContract::class, function (): HolderContract {
            return new Holder($this->partialManager);
        });

        $this->getContainer()->add(ImageLightboxContract::class, function (): ImageLightboxContract {
            return new ImageLightbox($this->partialManager);
        });

        $this->getContainer()->add(ModalContract::class, function (): ModalContract {
            return new Modal($this->partialManager);
        });

        $this->getContainer()->add(NoticeContract::class, function (): NoticeContract {
            return new Notice($this->partialManager);
        });

        $this->getContainer()->add(PaginationContract::class, function (): PaginationContract {
            return new Pagination($this->partialManager);
        });

        $this->getContainer()->add(PdfviewerContract::class, function (): PdfviewerContract {
            return new Pdfviewer($this->partialManager);
        });

        $this->getContainer()->add(SidebarContract::class, function (): SidebarContract {
            return new Sidebar($this->partialManager);
        });

        $this->getContainer()->add(SliderContract::class, function (): SliderContract {
            return new Slider($this->partialManager);
        });

        $this->getContainer()->add(SpinnerContract::class, function (): SpinnerContract {
            return new Spinner($this->partialManager);
        });

        $this->getContainer()->add(TabContract::class, function (): TabContract {
            return new Tab($this->partialManager);
        });

        $this->getContainer()->add(TableContract::class, function (): TableContract {
            return new Table($this->partialManager);
        });
    }
}