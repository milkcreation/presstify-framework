<?php declare(strict_types=1);

namespace tiFy\Metabox;

use tiFy\Container\ServiceProvider;
use tiFy\Contracts\Metabox\{MetaboxContext as MetaboxContextContract,
    MetaboxDriver as MetaboxDriverContract,
    MetaboxScreen as MetaboxScreenContract,
    MetaboxView as MetaboxViewContract};
use tiFy\Metabox\Contexts\{TabContext};
use tiFy\Metabox\Drivers\{
    Color\Color as ColorDriver,
    CustomHeader\CustomHeader as CustomHeaderDriver,
    Fileshare\Fileshare as FileshareDriver,
    Icon\Icon as IconDriver,
    Imagefeed\Imagefeed as ImagefeedDriver,
    Order\Order as OrderDriver,
    Postfeed\Postfeed as PostfeedDriver,
    RelatedTerm\RelatedTerm as RelatedTermDriver,
    Slideshow\Slideshow as SlideshowDriver,
    Subtitle\Subtitle as SubtitleDriver,
    TextRemaining\TextRemaining as TextRemainingDriver,
    Videofeed\Videofeed as VideofeedDriver
};

class MetaboxServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet un chargement différé des services.}
     * @var string[]
     */
    protected $provides = [
        'metabox',
        MetaboxContextContract::class,
        MetaboxDriverContract::class,
        MetaboxScreenContract::class,
        MetaboxViewContract::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('metabox', function () {
            return new MetaboxManager($this->getContainer());
        });

        $this->getContainer()->add(MetaboxContextContract::class, function () {
            return new MetaboxContext();
        });

        $this->getContainer()->add(MetaboxDriverContract::class, function () {
            return new MetaboxDriver();
        });

        $this->getContainer()->add(MetaboxScreenContract::class, function () {
            return new MetaboxScreen();
        });

        $this->registerContexts();

        $this->registerDrivers();
    }

    /**
     * @inheritDoc
     */
    public function registerContexts(): void
    {
        $this->getContainer()->add("metabox.context.tab", function () {
            return new TabContext();
        });
    }

    /**
     * @inheritDoc
     */
    public function registerDrivers(): void
    {
        $this->getContainer()->add('metabox.driver.color', function () {
            return new ColorDriver();
        });

        $this->getContainer()->add("metabox.driver.custom-header", function () {
            return new CustomHeaderDriver();
        });

        $this->getContainer()->add("metabox.driver.fileshare", function () {
            return new FileshareDriver();
        });

        $this->getContainer()->add("metabox.driver.icon", function () {
            return new IconDriver();
        });

        $this->getContainer()->add("metabox.driver.order", function () {
            return new OrderDriver();
        });

        $this->getContainer()->add("metabox.driver.imagefeed", function () {
            return new ImagefeedDriver();
        });

        $this->getContainer()->add("metabox.driver.postfeed", function () {
            return new PostfeedDriver();
        });

        $this->getContainer()->add("metabox.driver.related-term", function () {
            return new RelatedTermDriver();
        });

        $this->getContainer()->add("metabox.driver.slideshow", function () {
            return new SlideshowDriver();
        });

        $this->getContainer()->add("metabox.driver.subtitle", function () {
            return new SubtitleDriver();
        });

        $this->getContainer()->add("metabox.driver.text-remaining", function () {
            return new TextRemainingDriver();
        });

        $this->getContainer()->add("metabox.driver.videofeed", function () {
            return new VideofeedDriver();
        });
    }
}