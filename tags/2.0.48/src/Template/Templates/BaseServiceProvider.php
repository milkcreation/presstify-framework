<?php

namespace tiFy\Template\Templates;

use tiFy\Contracts\Template\TemplateFactory;
use tiFy\Kernel\Container\Container;
use tiFy\Kernel\Container\ServiceProvider;
use tiFy\View\ViewEngine;

class BaseServiceProvider extends ServiceProvider
{
    /**
     * Instance du gabarit d'affichage.
     * @var TemplateFactory
     */
    protected $template;

    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'assets',
        'db',
        'labels',
        'notices',
        'params',
        'request',
        'url',
        'viewer'
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param Container $container
     * @param TemplateFactory $template Instance du gabarit d'affichage associé.
     *
     * @return void
     */
    public function __construct(Container $container, TemplateFactory $template)
    {
        $this->template = $template;

        parent::__construct($container);
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->parseProvides();
    }

    /**
     * Récupération de l'alias de qualification complet d'un service fournis.
     *
     * @param $alias
     *
     * @return string
     */
    public function getFullAlias($alias)
    {
        return "template.factory.{$this->template->name()}.{$alias}";
    }

    /**
     * Traitement des préfixes des services fournis.
     *
     * @return void
     */
    public function parseProvides()
    {
        $this->provides = array_map(function($alias) {
                return $this->getFullAlias($alias);
            },
            $this->provides
        );
    }

    /**
     * {@inheritdoc}
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
        $this->getContainer()->share($this->getFullAlias('assets'), function(TemplateFactory $template) {
            return new BaseAssets($template);
        })->withArgument($this->template);
    }

    /**
     * Déclaration du controleur de base de données.
     *
     * @return void
     */
    public function registerDb()
    {
        $this->getContainer()->share($this->getFullAlias('db'), function(TemplateFactory $template) {
            if ($attrs = $template->config('db')) :
                return new BaseDb($template->name(), $attrs, $template);
            else :
                return null;
            endif;
        })->withArgument($this->template);
    }

    /**
     * Déclaration du controleur d'intitulés.
     *
     * @return void
     */
    public function registerLabels()
    {
        $this->getContainer()->share($this->getFullAlias('labels'), function(TemplateFactory $template) {
            return new BaseLabels($template->name(), $template->config('labels', []), $template);
        })->withArgument($this->template);
    }

    /**
     * Déclaration du controleur de messages de notification.
     *
     * @return void
     */
    public function registerNotices()
    {
        $this->getContainer()->share($this->getFullAlias('notices'), function(TemplateFactory $template) {
            return new BaseNotices($template);
        })->withArgument($this->template);
    }

    /**
     * Déclaration du controleur de paramètres.
     *
     * @return void
     */
    public function registerParams()
    {
        $this->getContainer()->share($this->getFullAlias('params'), function(TemplateFactory $template) {
            return new BaseParams($template->config('params', []), $template);
        })->withArgument($this->template);
    }

    /**
     * Déclaration du controleur de messages de requête HTTP.
     *
     * @return void
     */
    public function registerRequest()
    {
        $this->getContainer()->share($this->getFullAlias('request'), function(TemplateFactory $template) {
            return BaseRequest::capture()->setTemplate($template);
        })->withArgument($this->template);
    }

    /**
     * Déclaration du controleur des urls.
     *
     * @return void
     */
    public function registerUrl()
    {
        $this->getContainer()->share($this->getFullAlias('url'), function(TemplateFactory $template) {
            return new BaseUrl(router(), request(), $template);
        })->withArgument($this->template);
    }

    /**
     * Déclaration du controleur de gabarit d'affichage.
     *
     * @return void
     */
    public function registerViewer()
    {
        $this->getContainer()->share($this->getFullAlias('viewer'), function(TemplateFactory $template) {
            $params = $this->template->config('viewer', []);

            if (!$params instanceof ViewEngine) :
                $viewer = new ViewEngine(
                    array_merge(
                        [
                            'directory' => template()->resourcesDir('/views')
                        ],
                        $params
                    )
                );
                $viewer->setController(BaseViewer::class);

                if (!$viewer->getOverrideDir()) :
                    $viewer->setOverrideDir(template()->resourcesDir('/views'));
                endif;
            else :
                $viewer = $params;
            endif;

            $viewer->set('template', $template);

            return $viewer;
        })->withArgument($this->template);
    }
}