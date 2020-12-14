<?php declare(strict_types=1);

namespace tiFy\Partial;

use tiFy\Container\ServiceProvider;
use tiFy\Contracts\Partial\Accordion as AccordionContract;
use tiFy\Contracts\Partial\Breadcrumb as BreadcrumbContract;
use tiFy\Contracts\Partial\BurgerButton as BurgerButtonContract;
use tiFy\Contracts\Partial\CookieNotice as CookieNoticeContract;
use tiFy\Contracts\Partial\CurtainMenu as CurtainMenuContract;
use tiFy\Contracts\Partial\Dropdown as DropdownContract;
use tiFy\Contracts\Partial\Downloader as DownloaderContract;
use tiFy\Contracts\Partial\FlashNotice as FlashNoticeContract;
use tiFy\Contracts\Partial\Holder as HolderContract;
use tiFy\Contracts\Partial\ImageLightbox as ImageLightboxContract;
use tiFy\Contracts\Partial\MenuDriver as MenuDriverContract;
use tiFy\Contracts\Partial\Modal as ModalContract;
use tiFy\Contracts\Partial\Notice as NoticeContract;
use tiFy\Contracts\Partial\Pagination as PaginationContract;
use tiFy\Contracts\Partial\Partial as PartialManagerContract;
use tiFy\Contracts\Partial\Pdfviewer as PdfviewerContract;
use tiFy\Contracts\Partial\Progress as ProgressContract;
use tiFy\Contracts\Partial\Sidebar as SidebarContract;
use tiFy\Contracts\Partial\Slider as SliderContract;
use tiFy\Contracts\Partial\Spinner as SpinnerContract;
use tiFy\Contracts\Partial\Tab as TabContract;
use tiFy\Contracts\Partial\Table as TableContract;
use tiFy\Contracts\Partial\Tag as TagContract;
use tiFy\Partial\Driver\Accordion\Accordion;
use tiFy\Partial\Driver\Breadcrumb\Breadcrumb;
use tiFy\Partial\Driver\BurgerButton\BurgerButton;
use tiFy\Partial\Driver\CookieNotice\CookieNotice;
use tiFy\Partial\Driver\CurtainMenu\CurtainMenu;
use tiFy\Partial\Driver\Dropdown\Dropdown;
use tiFy\Partial\Driver\Downloader\Downloader;
use tiFy\Partial\Driver\FlashNotice\FlashNotice;
use tiFy\Partial\Driver\Holder\Holder;
use tiFy\Partial\Driver\ImageLightbox\ImageLightbox;
use tiFy\Partial\Driver\Menu\MenuDriver;
use tiFy\Partial\Driver\Modal\Modal;
use tiFy\Partial\Driver\Notice\Notice;
use tiFy\Partial\Driver\Pagination\Pagination;
use tiFy\Partial\Driver\Pdfviewer\Pdfviewer;
use tiFy\Partial\Driver\Progress\Progress;
use tiFy\Partial\Driver\Sidebar\Sidebar;
use tiFy\Partial\Driver\Slider\Slider;
use tiFy\Partial\Driver\Spinner\Spinner;
use tiFy\Partial\Driver\Tab\Tab;
use tiFy\Partial\Driver\Table\Table;
use tiFy\Partial\Driver\Tag\Tag;
use tiFy\Support\Proxy\View;

class PartialServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        PartialManagerContract::class,
        AccordionContract::class,
        BreadcrumbContract::class,
        BurgerButton::class,
        CookieNoticeContract::class,
        CurtainMenuContract::class,
        DropdownContract::class,
        FlashNoticeContract::class,
        HolderContract::class,
        ImageLightboxContract::class,
        MenuDriverContract::class,
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
        TagContract::class,
        'partial.view-engine'
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(PartialManagerContract::class, function (): PartialManagerContract {
            return new Partial(config('partial', []), $this->getContainer());
        });

        $this->registerDefaultDrivers();
        $this->registerViewEngine();
    }

    /**
     * Déclaration des pilotes par défaut.
     *
     * @return void
     */
    public function registerDefaultDrivers(): void
    {
        $this->getContainer()->add(AccordionContract::class, function (): AccordionContract {
            return new Accordion($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(BreadcrumbContract::class, function (): BreadcrumbContract {
            return new Breadcrumb($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(BurgerButtonContract::class, function (): BurgerButtonContract {
            return new BurgerButton($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(CookieNoticeContract::class, function (): CookieNoticeContract {
            return new CookieNotice($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(CurtainMenuContract::class, function (): CurtainMenuContract {
            return new CurtainMenu($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(DropdownContract::class, function (): DropdownContract {
            return new Dropdown($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(DownloaderContract::class, function (): DownloaderContract {
            return new Downloader($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(FlashNoticeContract::class, function (): FlashNoticeContract {
            return new FlashNotice($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(HolderContract::class, function (): HolderContract {
            return new Holder($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(ImageLightboxContract::class, function (): ImageLightboxContract {
            return new ImageLightbox($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(MenuDriverContract::class, function (): MenuDriverContract {
            return new MenuDriver($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(ModalContract::class, function (): ModalContract {
            return new Modal($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(NoticeContract::class, function (): NoticeContract {
            return new Notice($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(PaginationContract::class, function (): PaginationContract {
            return new Pagination($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(PdfviewerContract::class, function (): PdfviewerContract {
            return new Pdfviewer($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(ProgressContract::class, function (): ProgressContract {
            return new Progress($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(SidebarContract::class, function (): SidebarContract {
            return new Sidebar($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(SliderContract::class, function (): SliderContract {
            return new Slider($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(SpinnerContract::class, function (): SpinnerContract {
            return new Spinner($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(TabContract::class, function (): TabContract {
            return new Tab($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(TableContract::class, function (): TableContract {
            return new Table($this->getContainer()->get(PartialManagerContract::class));
        });

        $this->getContainer()->add(TagContract::class, function (): TagContract {
            return new Tag($this->getContainer()->get(PartialManagerContract::class));
        });
    }

    /**
     * Déclaration du moteur d'affichage.
     *
     * @return void
     */
    public function registerViewEngine(): void
    {
        $this->getContainer()->add('partial.view-engine', function () {
            return View::getPlatesEngine();
        });
    }
}