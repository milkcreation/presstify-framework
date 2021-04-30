<?php

declare(strict_types=1);

namespace tiFy\Partial;

use tiFy\Container\ServiceProvider;
use tiFy\Partial\Contracts\PartialContract;
use tiFy\Partial\Drivers\AccordionDriver;
use tiFy\Partial\Drivers\BreadcrumbDriver;
use tiFy\Partial\Drivers\BurgerButtonDriver;
use tiFy\Partial\Drivers\CookieNoticeDriver;
use tiFy\Partial\Drivers\CurtainMenuDriver;
use tiFy\Partial\Drivers\DropdownDriver;
use tiFy\Partial\Drivers\DownloaderDriver;
use tiFy\Partial\Drivers\FlashNoticeDriver;
use tiFy\Partial\Drivers\HolderDriver;
use tiFy\Partial\Drivers\ImageLightboxDriver;
use tiFy\Partial\Drivers\MenuDriver;
use tiFy\Partial\Drivers\ModalDriver;
use tiFy\Partial\Drivers\NoticeDriver;
use tiFy\Partial\Drivers\PaginationDriver;
use tiFy\Partial\Drivers\PdfViewerDriver;
use tiFy\Partial\Drivers\ProgressDriver;
use tiFy\Partial\Drivers\SidebarDriver;
use tiFy\Partial\Drivers\SliderDriver;
use tiFy\Partial\Drivers\SpinnerDriver;
use tiFy\Partial\Drivers\TabDriver;
use tiFy\Partial\Drivers\TableDriver;
use tiFy\Partial\Drivers\TagDriver;
use tiFy\Support\Proxy\View;

class PartialServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        PartialContract::class,
        AccordionDriver::class,
        BreadcrumbDriver::class,
        BurgerButtonDriver::class,
        CookieNoticeDriver::class,
        CurtainMenuDriver::class,
        DropdownDriver::class,
        FlashNoticeDriver::class,
        HolderDriver::class,
        ImageLightboxDriver::class,
        MenuDriver::class,
        ModalDriver::class,
        NoticeDriver::class,
        PaginationDriver::class,
        PdfViewerDriver::class,
        ProgressDriver::class,
        SidebarDriver::class,
        SliderDriver::class,
        SpinnerDriver::class,
        TabDriver::class,
        TableDriver::class,
        TagDriver::class,
        'partial.view-engine',
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            PartialContract::class,
            function () {
                return new Partial(config('partial', []), $this->getContainer());
            }
        );
        $this->registerDrivers();
        $this->registerViewEngine();
    }

    /**
     * Déclaration des pilotes par défaut.
     *
     * @return void
     */
    public function registerDrivers(): void
    {
        $this->getContainer()->add(
            AccordionDriver::class,
            function () {
                return new AccordionDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            BreadcrumbDriver::class,
            function () {
                return new BreadcrumbDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            BurgerButtonDriver::class,
            function () {
                return new BurgerButtonDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            CookieNoticeDriver::class,
            function () {
                return new CookieNoticeDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            CurtainMenuDriver::class,
            function () {
                return new CurtainMenuDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            DropdownDriver::class,
            function () {
                return new DropdownDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            DownloaderDriver::class,
            function () {
                return new DownloaderDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            FlashNoticeDriver::class,
            function () {
                return new FlashNoticeDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            HolderDriver::class,
            function () {
                return new HolderDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            ImageLightboxDriver::class,
            function () {
                return new ImageLightboxDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            MenuDriver::class,
            function () {
                return new MenuDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            ModalDriver::class,
            function () {
                return new ModalDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            NoticeDriver::class,
            function () {
                return new NoticeDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            PaginationDriver::class,
            function () {
                return new PaginationDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            PdfviewerDriver::class,
            function () {
                return new PdfViewerDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            ProgressDriver::class,
            function () {
                return new ProgressDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            SidebarDriver::class,
            function () {
                return new SidebarDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            SliderDriver::class,
            function () {
                return new SliderDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            SpinnerDriver::class,
            function () {
                return new SpinnerDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            TabDriver::class,
            function () {
                return new TabDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            TableDriver::class,
            function () {
                return new TableDriver($this->getContainer()->get(PartialContract::class));
            }
        );
        $this->getContainer()->add(
            TagDriver::class,
            function () {
                return new TagDriver($this->getContainer()->get(PartialContract::class));
            }
        );
    }

    /**
     * Déclaration du moteur d'affichage.
     *
     * @return void
     */
    public function registerViewEngine(): void
    {
        $this->getContainer()->add(
            'partial.view-engine',
            function () {
                return View::getPlatesEngine();
            }
        );
    }
}