<?php declare(strict_types=1);

namespace tiFy\Metabox;

use tiFy\Container\ServiceProvider;
use tiFy\Contracts\Metabox\Metabox as MetaboxContract;
use tiFy\Contracts\Metabox\MetaboxContext as MetaboxContextContract;
use tiFy\Contracts\Metabox\MetaboxDriver as MetaboxDriverContract;
use tiFy\Contracts\Metabox\MetaboxScreen as MetaboxScreenContract;
use tiFy\Contracts\Metabox\ColorDriver as ColorDriverContract;
use tiFy\Contracts\Metabox\CustomHeaderDriver as CustomHeaderContract;
use tiFy\Contracts\Metabox\ExcerptDriver as ExcerptDriverContract;
use tiFy\Contracts\Metabox\FilefeedDriver as FilefeedContract;
use tiFy\Contracts\Metabox\IconDriver as IconDriverContract;
use tiFy\Contracts\Metabox\ImagefeedDriver as ImagefeedDriverContract;
use tiFy\Contracts\Metabox\OrderDriver as OrderDriverContract;
use tiFy\Contracts\Metabox\PostfeedDriver as PostfeedDriverContract;
use tiFy\Contracts\Metabox\RelatedTermDriver as RelatedTermDriverContract;
use tiFy\Contracts\Metabox\SlidefeedDriver as SlidefeedDriverContract;
use tiFy\Contracts\Metabox\SubtitleDriver as SubtitleDriverContract;
use tiFy\Contracts\Metabox\TabContext as TabContextContract;
use tiFy\Contracts\Metabox\VideofeedDriver as VideofeedDriverContract;
use tiFy\Contracts\View\Engine as ViewEngineContract;
use tiFy\Metabox\Context\TabContext;
use tiFy\Metabox\Driver\Color\Color as ColorDriver;
use tiFy\Metabox\Driver\CustomHeader\CustomHeader as CustomHeaderDriver;
use tiFy\Metabox\Driver\Excerpt\Excerpt as ExcerptDriver;
use tiFy\Metabox\Driver\Filefeed\Filefeed as FilefeedDriver;
use tiFy\Metabox\Driver\Icon\Icon as IconDriver;
use tiFy\Metabox\Driver\Imagefeed\Imagefeed as ImagefeedDriver;
use tiFy\Metabox\Driver\Order\Order as OrderDriver;
use tiFy\Metabox\Driver\Postfeed\Postfeed as PostfeedDriver;
use tiFy\Metabox\Driver\RelatedTerm\RelatedTerm as RelatedTermDriver;
use tiFy\Metabox\Driver\Slidefeed\Slidefeed as SlidefeedDriver;
use tiFy\Metabox\Driver\Subtitle\Subtitle as SubtitleDriver;
use tiFy\Metabox\Driver\Videofeed\Videofeed as VideofeedDriver;
use tiFy\Support\Proxy\View;

class MetaboxServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet un chargement différé des services.}
     * @var string[]
     */
    protected $provides = [
        'metabox',
        'metabox.driver',
        ColorDriverContract::class,
        CustomHeaderContract::class,
        ExcerptDriverContract::class,
        FilefeedContract::class,
        IconDriverContract::class,
        ImagefeedDriverContract::class,
        OrderDriverContract::class,
        PostfeedDriverContract::class,
        RelatedTermDriverContract::class,
        SlidefeedDriverContract::class,
        SubtitleDriverContract::class,
        TabContextContract::class,
        VideofeedDriverContract::class,
        'metabox.context',
        'metabox.screen',
        'metabox.view-engine.context',
        'metabox.view-engine.driver',
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('metabox', function (): MetaboxContract {
            return (new Metabox(config('metabox', []), $this->getContainer()))->boot();
        });

        $this->getContainer()->add('metabox.context', function (): MetaboxContextContract {
            return new MetaboxContext();
        });

        $this->getContainer()->add('metabox.driver', function (): MetaboxDriverContract {
            return new MetaboxDriver();
        });

        $this->getContainer()->add('metabox.screen', function (): MetaboxScreenContract {
            return new MetaboxScreen();
        });

        $this->registerContexts();
        $this->registerDrivers();
        $this->registerContextViewEngine();
        $this->registerDriverViewEngine();
    }

    /**
     * Déclaration des contextes d'affichage.
     *
     * @return void
     */
    public function registerContexts(): void
    {
        $this->getContainer()->add(TabContextContract::class, function (): MetaboxContextContract {
            return new TabContext();
        });
    }

    /**
     * Déclaration des pilotes d'affichage.
     *
     * @return void
     */
    public function registerDrivers(): void
    {
        $this->getContainer()->add(ColorDriverContract::class, function (): MetaboxDriverContract {
            return new ColorDriver();
        });

        $this->getContainer()->add(CustomHeaderContract::class, function (): MetaboxDriverContract {
            return new CustomHeaderDriver();
        });

        $this->getContainer()->add(ExcerptDriverContract::class, function (): MetaboxDriverContract {
            return new ExcerptDriver();
        });

        $this->getContainer()->add(FilefeedContract::class, function (): MetaboxDriverContract {
            return new FilefeedDriver();
        });

        $this->getContainer()->add(IconDriverContract::class, function (): MetaboxDriverContract {
            return new IconDriver();
        });

        $this->getContainer()->add(OrderDriverContract::class, function (): MetaboxDriverContract {
            return new OrderDriver();
        });

        $this->getContainer()->add(ImagefeedDriverContract::class, function (): MetaboxDriverContract {
            return new ImagefeedDriver();
        });

        $this->getContainer()->add(PostfeedDriverContract::class, function (): MetaboxDriverContract {
            return new PostfeedDriver();
        });

        $this->getContainer()->add(RelatedTermDriverContract::class, function (): MetaboxDriverContract {
            return new RelatedTermDriver();
        });

        $this->getContainer()->add(SlidefeedDriverContract::class, function (): MetaboxDriverContract {
            return new SlidefeedDriver();
        });

        $this->getContainer()->add(SubtitleDriverContract::class, function (): MetaboxDriverContract {
            return new SubtitleDriver();
        });

        $this->getContainer()->add(VideofeedDriverContract::class, function (): MetaboxDriverContract {
            return new VideofeedDriver();
        });
    }

    /**
     * Déclaration du moteur d'affichage de contexte.
     *
     * @return void
     */
    public function registerContextViewEngine(): void
    {
        $this->getContainer()->add('metabox.view-engine.context', function (): ViewEngineContract {
            return View::getPlatesEngine();
        });
    }

    /**
     * Déclaration du moteur d'affichage de pilote.
     *
     * @return void
     */
    public function registerDriverViewEngine(): void
    {
        $this->getContainer()->add('metabox.view-engine.driver', function (): ViewEngineContract {
            return View::getPlatesEngine();
        });
    }
}