<?php

namespace tiFy\View\Pattern;

use tiFy\Contracts\View\ViewPatternController;
use tiFy\Kernel\Container\Container;
use tiFy\Kernel\Container\ServiceProvider;
use tiFy\View\ViewEngine;

class PatternServiceProvider extends ServiceProvider
{
    /**
     * Instance du controleur du motif d'affichage associé.
     * @var ViewPatternController
     */
    protected $pattern;

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
     * @param ViewPatternController $pattern Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct(Container $container, ViewPatternController $pattern)
    {
        $this->pattern = $pattern;

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
        return "view.pattern.{$this->pattern->name()}.{$alias}";
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
        $this->getContainer()->share($this->getFullAlias('assets'), function(ViewPatternController $pattern) {
            return new PatternBaseAssets($pattern);
        })->withArgument($this->pattern);
    }

    /**
     * Déclaration du controleur de base de données.
     *
     * @return void
     */
    public function registerDb()
    {
        $this->getContainer()->share($this->getFullAlias('db'), function(ViewPatternController $pattern) {
            if ($attrs = $pattern->config('db')) :
                return new PatternBaseDb($pattern->name(), $attrs, $pattern);
            else :
                return null;
            endif;
        })->withArgument($this->pattern);
    }

    /**
     * Déclaration du controleur d'intitulés.
     *
     * @return void
     */
    public function registerLabels()
    {
        $this->getContainer()->share($this->getFullAlias('labels'), function(ViewPatternController $pattern) {
            return new PatternBaseLabels($pattern->name(), $pattern->config('labels', []), $pattern);
        })->withArgument($this->pattern);
    }

    /**
     * Déclaration du controleur de messages de notification.
     *
     * @return void
     */
    public function registerNotices()
    {
        $this->getContainer()->share($this->getFullAlias('notices'), function(ViewPatternController $pattern) {
            return new PatternBaseNotices($pattern);
        })->withArgument($this->pattern);
    }

    /**
     * Déclaration du controleur de paramètres.
     *
     * @return void
     */
    public function registerParams()
    {
        $this->getContainer()->share($this->getFullAlias('params'), function(ViewPatternController $pattern) {
            return new PatternBaseParams($pattern->config('params', []), $pattern);
        })->withArgument($this->pattern);
    }

    /**
     * Déclaration du controleur de messages de requête HTTP.
     *
     * @return void
     */
    public function registerRequest()
    {
        $this->getContainer()->share($this->getFullAlias('request'), function(ViewPatternController $pattern) {
            return PatternBaseRequest::capture()->setPattern($pattern);
        })->withArgument($this->pattern);
    }

    /**
     * Déclaration du controleur des urls.
     *
     * @return void
     */
    public function registerUrl()
    {
        $this->getContainer()->share($this->getFullAlias('url'), function(ViewPatternController $pattern) {
            return new PatternBaseUrl(router(), request(), $pattern);
        })->withArgument($this->pattern);
    }

    /**
     * Déclaration du controleur de gabarit d'affichage.
     *
     * @return void
     */
    public function registerViewer()
    {
        $this->getContainer()->share($this->getFullAlias('viewer'), function(ViewPatternController $pattern) {
            $params = $this->pattern->config('viewer', []);

            if (!$params instanceof ViewEngine) :
                $viewer = new ViewEngine(
                    array_merge(
                        [
                            'directory' => pattern()->resourcesDir('/views')
                        ],
                        $params
                    )
                );
                $viewer->setController(PatternBaseViewer::class);

                if (!$viewer->getOverrideDir()) :
                    $viewer->setOverrideDir(pattern()->resourcesDir('/views'));
                endif;
            else :
                $viewer = $params;
            endif;

            $viewer->set('pattern', $pattern);

            return $viewer;
        })->withArgument($this->pattern);
    }
}