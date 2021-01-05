<?php

declare(strict_types=1);

namespace tiFy\Metabox;

use tiFy\Container\ServiceProvider;
use tiFy\Metabox\Contexts\TabContext;
use tiFy\Metabox\Contracts\MetaboxContract;
use tiFy\Metabox\Drivers\ColorDriver;
use tiFy\Metabox\Drivers\CustomHeaderDriver;
use tiFy\Metabox\Drivers\ExcerptDriver;
use tiFy\Metabox\Drivers\FilefeedDriver;
use tiFy\Metabox\Drivers\IconDriver;
use tiFy\Metabox\Drivers\ImagefeedDriver;
use tiFy\Metabox\Drivers\OrderDriver;
use tiFy\Metabox\Drivers\PostfeedDriver;
use tiFy\Metabox\Drivers\RelatedTermDriver;
use tiFy\Metabox\Drivers\SlidefeedDriver;
use tiFy\Metabox\Drivers\SubtitleDriver;
use tiFy\Metabox\Drivers\VideofeedDriver;
use tiFy\Support\Proxy\View;

class MetaboxServiceProvider extends ServiceProvider
{
    /**
     * @inheritDoc
     */
    protected $provides = [
        MetaboxContract::class,
        MetaboxContext::class,
        MetaboxDriver::class,
        MetaboxScreen::class,
        ColorDriver::class,
        CustomHeaderDriver::class,
        ExcerptDriver::class,
        FilefeedDriver::class,
        IconDriver::class,
        ImagefeedDriver::class,
        OrderDriver::class,
        PostfeedDriver::class,
        RelatedTermDriver::class,
        SlidefeedDriver::class,
        SubtitleDriver::class,
        TabContext::class,
        VideofeedDriver::class,
        'metabox.view-engine.context',
        'metabox.view-engine.driver',
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            MetaboxContract::class,
            function () {
                return new Metabox(config('metabox', []), $this->getContainer());
            }
        );

        $this->getContainer()->add(
            MetaboxContext::class,
            function () {
                return new MetaboxContext($this->getContainer()->get(MetaboxContract::class));
            }
        );

        $this->getContainer()->add(
            MetaboxDriver::class,
            function () {
                return new MetaboxDriver($this->getContainer()->get(MetaboxContract::class));
            }
        );

        $this->getContainer()->add(
            MetaboxScreen::class,
            function () {
                return new MetaboxScreen($this->getContainer()->get(MetaboxContract::class));
            }
        );

        $this->registerContexts();
        $this->registerDrivers();
        $this->registerContextViewEngine();
        $this->registerDriverViewEngine();
    }

    /**
     * Déclaration des contextes.
     *
     * @return void
     */
    public function registerContexts(): void
    {
        $this->getContainer()->add(
            TabContext::class,
            function () {
                return new TabContext($this->getContainer()->get(MetaboxContract::class));
            }
        );
    }

    /**
     * Déclaration des pilotes.
     *
     * @return void
     */
    public function registerDrivers(): void
    {
        $this->getContainer()->add(
            ColorDriver::class,
            function () {
                return new ColorDriver($this->getContainer()->get(MetaboxContract::class));
            }
        );
        $this->getContainer()->add(
            CustomHeaderDriver::class,
            function () {
                return new CustomHeaderDriver($this->getContainer()->get(MetaboxContract::class));
            }
        );
        $this->getContainer()->add(
            ExcerptDriver::class,
            function () {
                return new ExcerptDriver($this->getContainer()->get(MetaboxContract::class));
            }
        );
        $this->getContainer()->add(
            FilefeedDriver::class,
            function () {
                return new FilefeedDriver($this->getContainer()->get(MetaboxContract::class));
            }
        );
        $this->getContainer()->add(
            IconDriver::class,
            function () {
                return new IconDriver($this->getContainer()->get(MetaboxContract::class));
            }
        );
        $this->getContainer()->add(
            OrderDriver::class,
            function () {
                return new OrderDriver($this->getContainer()->get(MetaboxContract::class));
            }
        );
        $this->getContainer()->add(
            ImagefeedDriver::class,
            function () {
                return new ImagefeedDriver($this->getContainer()->get(MetaboxContract::class));
            }
        );
        $this->getContainer()->add(
            PostfeedDriver::class,
            function () {
                return new PostfeedDriver($this->getContainer()->get(MetaboxContract::class));
            }
        );
        $this->getContainer()->add(
            RelatedTermDriver::class,
            function () {
                return new RelatedTermDriver($this->getContainer()->get(MetaboxContract::class));
            }
        );
        $this->getContainer()->add(
            SlidefeedDriver::class,
            function () {
                return new SlidefeedDriver($this->getContainer()->get(MetaboxContract::class));
            }
        );
        $this->getContainer()->add(
            SubtitleDriver::class,
            function () {
                return new SubtitleDriver($this->getContainer()->get(MetaboxContract::class));
            }
        );
        $this->getContainer()->add(
            VideofeedDriver::class,
            function () {
                return new VideofeedDriver($this->getContainer()->get(MetaboxContract::class));
            }
        );
    }

    /**
     * Déclaration du moteur d'affichage de contexte.
     *
     * @return void
     */
    public function registerContextViewEngine(): void
    {
        $this->getContainer()->add(
            'metabox.view-engine.context',
            function () {
                return View::getPlatesEngine();
            }
        );
    }

    /**
     * Déclaration du moteur d'affichage de pilote.
     *
     * @return void
     */
    public function registerDriverViewEngine(): void
    {
        $this->getContainer()->add(
            'metabox.view-engine.driver',
            function () {
                return View::getPlatesEngine();
            }
        );
    }
}