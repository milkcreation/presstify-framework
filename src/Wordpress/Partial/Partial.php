<?php declare(strict_types=1);

namespace tiFy\Wordpress\Partial;

use Psr\Container\ContainerInterface as Container;
use tiFy\Partial\Contracts\PartialContract;
use tiFy\Partial\Drivers\BreadcrumbDriver as BaseBreadcrumbDriver;
use tiFy\Partial\Drivers\CurtainMenuDriver as BaseCurtainMenuDriver;
use tiFy\Partial\Drivers\DownloaderDriver as BaseDownloaderDriver;
use tiFy\Partial\Drivers\ImageLightboxDriver as BaseImageLightboxDriver;
use tiFy\Partial\Drivers\ModalDriver as BaseModalDriver;
use tiFy\Partial\Drivers\PaginationDriver as BasePaginationDriver;
use tiFy\Partial\Drivers\PdfViewerDriver as BasePdfViewerDriver;
use tiFy\Support\Concerns\ContainerAwareTrait;
use tiFy\Wordpress\Partial\Drivers\BreadcrumbDriver;
use tiFy\Wordpress\Partial\Drivers\CurtainMenuDriver;
use tiFy\Wordpress\Partial\Drivers\DownloaderDriver;
use tiFy\Wordpress\Partial\Drivers\ImageLightboxDriver;
use tiFy\Wordpress\Partial\Drivers\MediaLibraryDriver;
use tiFy\Wordpress\Partial\Drivers\ModalDriver;
use tiFy\Wordpress\Partial\Drivers\PaginationDriver;
use tiFy\Wordpress\Partial\Drivers\PdfViewerDriver;

class Partial
{
    use ContainerAwareTrait;

    /**
     * Définition des pilotes spécifiques à Wordpress.
     * @var array
     */
    protected $drivers = [
        'media-library' => MediaLibraryDriver::class,
    ];

    /**
     * Instance du gestionnaire des portions d'affichage.
     * @var PartialContract
     */
    protected $partialManager;

    /**
     * @param PartialContract $partialManager
     * @param Container $container
     */
    public function __construct(PartialContract $partialManager, Container $container)
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
        $this->getContainer()->add(MediaLibraryDriver::class, function () {
            return new MediaLibraryDriver($this->partialManager);
        });
    }

    /**
     * Déclaration des controleurs de surchage des portions d'affichage.
     *
     * @return void
     */
    public function registerOverride(): void
    {
        $this->getContainer()->add(BaseBreadcrumbDriver::class, function () {
            return new BreadcrumbDriver($this->partialManager);
        });
        $this->getContainer()->add(BaseCurtainMenuDriver::class, function () {
            return new CurtainMenuDriver($this->partialManager);
        });
        $this->getContainer()->add(BaseDownloaderDriver::class, function () {
            return new DownloaderDriver($this->partialManager);
        });
        $this->getContainer()->add(BaseImageLightboxDriver::class, function () {
            return new ImageLightboxDriver($this->partialManager);
        });
        $this->getContainer()->add(BaseModalDriver::class, function () {
            return new ModalDriver($this->partialManager);
        });
        $this->getContainer()->add(BasePaginationDriver::class, function () {
            return new PaginationDriver($this->partialManager);
        });
        $this->getContainer()->add(BasePdfviewerDriver::class, function () {
            return new PdfViewerDriver($this->partialManager);
        });
    }
}