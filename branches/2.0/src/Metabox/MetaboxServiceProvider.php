<?php declare(strict_types=1);

namespace tiFy\Metabox;

use tiFy\Container\ServiceProvider;
use tiFy\Contracts\Metabox\{
    MetaboxContext as MetaboxContextContract,
    MetaboxDriver as MetaboxDriverContract,
    MetaboxManager as MetaboxManagerContract,
    MetaboxScreen as MetaboxScreenContract,
    MetaboxView as MetaboxViewContract
};
use tiFy\Metabox\Context\TabContext;
use tiFy\Metabox\Driver\{
    Color\Color as ColorDriver,
    CustomHeader\CustomHeader as CustomHeaderDriver,
    Excerpt\Excerpt as ExcerptDriver,
    Filefeed\Filefeed as FilefeedDriver,
    Icon\Icon as IconDriver,
    Imagefeed\Imagefeed as ImagefeedDriver,
    Order\Order as OrderDriver,
    Postfeed\Postfeed as PostfeedDriver,
    RelatedTerm\RelatedTerm as RelatedTermDriver,
    Slidefeed\Slidefeed as SlidefeedDriver,
    Subtitle\Subtitle as SubtitleDriver,
    Videofeed\Videofeed as VideofeedDriver
};
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
        'metabox.driver.color',
        'metabox.driver.custom-header',
        'metabox.driver.excerpt',
        'metabox.driver.filefeed',
        'metabox.driver.icon',
        'metabox.driver.order',
        'metabox.driver.imagefeed',
        'metabox.driver.postfeed',
        'metabox.driver.related-term',
        'metabox.driver.slidefeed',
        'metabox.driver.subtitle',
        'metabox.driver.videofeed',
        MetaboxContextContract::class,
        MetaboxDriverContract::class,
        MetaboxScreenContract::class,
        MetaboxViewContract::class,
        'metabox.viewer'
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

        $this->registerContext();

        $this->registerDriver();

        $this->registerViewer();
    }

    /**
     * Déclaration des services de contexte d'affichage.
     *
     * @return void
     */
    public function registerContext(): void
    {
        $this->getContainer()->add("metabox.context.tab", function () {
            return new TabContext();
        });
    }

    /**
     * Déclaration de la collection de pilote d'affichage.
     *
     * @return void
     */
    public function registerDriver(): void
    {
        $this->getContainer()->add('metabox.driver.color', function () {
            return new ColorDriver();
        });

        $this->getContainer()->add('metabox.driver.custom-header', function () {
            return new CustomHeaderDriver();
        });

        $this->getContainer()->add('metabox.driver.excerpt', function () {
            return new ExcerptDriver();
        });

        $this->getContainer()->add('metabox.driver.filefeed', function () {
            return new FilefeedDriver();
        });

        $this->getContainer()->add('metabox.driver.icon', function () {
            return new IconDriver();
        });

        $this->getContainer()->add('metabox.driver.order', function () {
            return new OrderDriver();
        });

        $this->getContainer()->add('metabox.driver.imagefeed', function () {
            return new ImagefeedDriver();
        });

        $this->getContainer()->add('metabox.driver.postfeed', function () {
            return new PostfeedDriver();
        });

        $this->getContainer()->add('metabox.driver.related-term', function () {
            return new RelatedTermDriver();
        });

        $this->getContainer()->add('metabox.driver.slidefeed', function () {
            return new SlidefeedDriver();
        });

        $this->getContainer()->add('metabox.driver.subtitle', function () {
            return new SubtitleDriver();
        });

        $this->getContainer()->add('metabox.driver.videofeed', function () {
            return new VideofeedDriver();
        });
    }

    /**
     * Déclaration du gestionnaire d'affichage.
     *
     * @return void
     */
    public function registerViewer(): void
    {
        $this->getContainer()->add('metabox.viewer', function (MetaboxDriver $driver) {
            /** @var MetaboxManagerContract $manager */
            $manager = $this->getContainer()->get('metabox');

            $config = config('metabox.viewer', []);

            if (isset($config['directory'])) {
                $config['directory'] = rtrim($config['directory'], '/') . '/' . $driver->getAlias();

                if (!file_exists($config['directory'])) {
                    unset($config['directory']);
                }
            }

            if (isset($config['override_dir'])) {
                $config['override_dir'] = rtrim($config['override_dir'], '/') . '/' . $driver->getAlias();

                if (!file_exists($config['override_dir'])) {
                    unset($config['override_dir']);
                }
            }

            return View::getPlatesEngine(array_merge([
                'directory'    => $manager->resourcesDir("/views/driver/{$driver->getAlias()}"),
                'factory'      => MetaboxView::class,
                'metabox'      => $driver,
            ], $config, $driver->get('viewer', [])));
        });
    }
}