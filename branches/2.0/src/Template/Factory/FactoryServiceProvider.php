<?php declare(strict_types=1);

namespace tiFy\Template\Factory;

use tiFy\Contracts\Template\{
    FactoryDb as FactoryDbContract,
    FactoryServiceProvider as FactoryServiceProviderContract,
    TemplateFactory};
use tiFy\Container\ServiceProvider;
use tiFy\View\ViewEngine;

class FactoryServiceProvider extends ServiceProvider implements FactoryServiceProviderContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit d'affichage.
     * @var TemplateFactory
     */
    protected $factory;

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        events()->listen('template.factory.boot', function () {
            $this->registerFactories();
        }, 100);
    }

    /**
     * @inheritDoc
     */
    public function getFactoryAlias(string $alias): string
    {
        return "template.factory.{$this->factory->name()}.{$alias}";
    }

    /**
     * @inheritDoc
     */
    public function registerFactories(): void
    {
        $this->registerFactoryAssets();
        $this->registerFactoryBuilder();
        $this->registerFactoryCache();
        $this->registerFactoryDb();
        $this->registerFactoryHttpController();
        $this->registerFactoryHttpXhrController();
        $this->registerFactoryLabels();
        $this->registerFactoryParams();
        $this->registerFactoryNotices();
        $this->registerFactoryRequest();
        $this->registerFactoryUrl();
        $this->registerFactoryViewer();
    }

    /**
     * Déclaration du controleur des assets.
     *
     * @return void
     */
    public function registerFactoryAssets(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('assets'), function () {
            return (new FactoryAssets())->setTemplateFactory($this->factory);
        });
    }

    /**
     * Déclaration du controleur de construction de requête.
     *
     * @return void
     */
    public function registerFactoryBuilder(): void
    {
        $this->getContainer()->add($this->getFactoryAlias('builder'), function () {
            $attrs = $this->factory->param('query_args', []);

            return (new FactoryBuilder())->setTemplateFactory($this->factory)->set(is_array($attrs) ? $attrs : []);
        });
    }

    /**
     * Déclaration du controleur de cache.
     *
     * @return void
     */
    public function registerFactoryCache(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('cache'), function () {
            return (new FactoryCache())->setTemplateFactory($this->factory);
        });
    }

    /**
     * Déclaration du controleur de base de données.
     *
     * @return void
     */
    public function registerFactoryDb(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('db'), function (): ?FactoryDbContract {

            if ($db = $this->factory->get('providers.db', [])) {
                $db = $db instanceof FactoryDbContract
                    ? $db
                    : new FactoryDb();
                return $db->setTemplateFactory($this->factory);
            } else {
                return null;
            }
        });
    }

    /**
     * Déclaration du controleur de requête HTTP.
     *
     * @return void
     */
    public function registerFactoryHttpController(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('controller'), function () {
            return (new FactoryHttpController())->setTemplateFactory($this->factory);
        });
    }

    /**
     * Déclaration du controleur de requête HTTP XHR.
     *
     * @return void
     */
    public function registerFactoryHttpXhrController(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('xhr'), function () {
            return (new FactoryHttpXhrController())->setTemplateFactory($this->factory);
        });
    }

    /**
     * Déclaration du controleur d'intitulés.
     *
     * @return void
     */
    public function registerFactoryLabels(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('labels'), function () {
            return (new FactoryLabels())->setTemplateFactory($this->factory)
                ->setName($this->factory->name())
                ->set($this->factory->get('labels', []))
                ->parse();
        });
    }

    /**
     * Déclaration du controleur de messages de notification.
     *
     * @return void
     */
    public function registerFactoryNotices(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('notices'), function () {
            return (new FactoryNotices())->setTemplateFactory($this->factory);
        });
    }

    /**
     * Déclaration du controleur de paramètres.
     *
     * @return void
     */
    public function registerFactoryParams(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('params'), function () {
            $attrs = $this->factory->get('params', []);

            return (new FactoryParams())->setTemplateFactory($this->factory)
                ->set(is_array($attrs) ? $attrs : [])->parse();
        });
    }

    /**
     * Déclaration du controleur de messages de requête HTTP.
     *
     * @return void
     */
    public function registerFactoryRequest(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('request'), function () {
            return FactoryRequest::capture()->setTemplateFactory($this->factory);
        });
    }

    /**
     * Déclaration du controleur des urls.
     *
     * @return void
     */
    public function registerFactoryUrl(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('url'), function () {
            return (new FactoryUrl())->setTemplateFactory($this->factory);
        });
    }

    /**
     * Déclaration du controleur de gabarit d'affichage.
     *
     * @return void
     */
    public function registerFactoryViewer(): void
    {
        $this->getContainer()->share($this->getFactoryAlias('viewer'), function () {
            $params = $this->factory->get('viewer', []);

            if (!$params instanceof ViewEngine) {
                $viewer = new ViewEngine(array_merge([
                    'directory' => template()->resourcesDir('/views')
                ], $params));

                $viewer->setController(FactoryViewer::class);

                if (!$viewer->getOverrideDir()) {
                    $viewer->setOverrideDir(template()->resourcesDir('/views'));
                }
            } else {
                $viewer = $params;
            }

            $viewer->set('factory', $this->factory);

            return $viewer;
        });
    }
}