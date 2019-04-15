<?php declare(strict_types=1);

namespace tiFy\Template\Factory;

use tiFy\Contracts\Db\DbFactory;
use tiFy\Contracts\Template\TemplateFactory;
use tiFy\Container\ServiceProvider;
use tiFy\View\ViewEngine;

class FactoryServiceProvider extends ServiceProvider
{
    /**
     * Instance du gabarit d'affichage.
     * @var TemplateFactory
     */
    protected $factory;

    /**
     * CONSTRUCTEUR.
     *
     * @param TemplateFactory $factory Instance du gabarit d'affichage associé.
     *
     * @return void
     */
    public function __construct(TemplateFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @inheritdoc
     */
    public function boot()
    {
        events()->on('template.factory.boot.'. $this->factory->name(), function () {
            $this->register();
        });
    }

    /**
     * Récupération de l'alias de qualification complet d'un service fournis.
     *
     * @param string $alias Nom de qualification court.
     *
     * @return string
     */
    public function getFullAlias(string $alias)
    {
        return "template.factory.{$this->factory->name()}.{$alias}";
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerAssets();
        $this->registerDb();
        $this->registerLabels();
        $this->registerParams();
        $this->registerNotices();
        $this->registerRequest();
        $this->registerUrl();
        $this->registerViewer();
    }

    /**
     * Déclaration du controleur des assets.
     *
     * @return void
     */
    public function registerAssets()
    {
        $this->getContainer()->share($this->getFullAlias('assets'), function() {
            return new FactoryAssets($this->factory);
        });
    }

    /**
     * Déclaration du controleur de base de données.
     *
     * @return void
     */
    public function registerDb()
    {
        $this->getContainer()->share($this->getFullAlias('db'), function() {
            if ($db = $this->factory->config('providers.db', [])) {
                return $db instanceof DbFactory ? $db : new FactoryDb($this->factory);
            } else {
                return null;
            }
        });
    }

    /**
     * Déclaration du controleur d'intitulés.
     *
     * @return void
     */
    public function registerLabels()
    {
        $this->getContainer()->share($this->getFullAlias('labels'), function() {
            return new FactoryLabels($this->factory);
        });
    }

    /**
     * Déclaration du controleur de messages de notification.
     *
     * @return void
     */
    public function registerNotices()
    {
        $this->getContainer()->share($this->getFullAlias('notices'), function() {
            return new FactoryNotices($this->factory);
        });
    }

    /**
     * Déclaration du controleur de paramètres.
     *
     * @return void
     */
    public function registerParams()
    {
        $this->getContainer()->share($this->getFullAlias('params'), function() {
            return new FactoryParams($this->factory);
        });
    }

    /**
     * Déclaration du controleur de messages de requête HTTP.
     *
     * @return void
     */
    public function registerRequest()
    {
        $this->getContainer()->share($this->getFullAlias('request'), function() {
            return FactoryRequest::capture()->setTemplateFactory($this->factory);
        });
    }

    /**
     * Déclaration du controleur des urls.
     *
     * @return void
     */
    public function registerUrl()
    {
        $this->getContainer()->share($this->getFullAlias('url'), function() {
            return new FactoryUrl($this->factory);
        });
    }

    /**
     * Déclaration du controleur de gabarit d'affichage.
     *
     * @return void
     */
    public function registerViewer()
    {
        $this->getContainer()->share($this->getFullAlias('viewer'), function() {
            $params = $this->factory->config('viewer', []);

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