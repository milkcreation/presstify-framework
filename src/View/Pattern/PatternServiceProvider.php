<?php

namespace tiFy\View\Pattern;

use tiFy\Db\DbItemBaseController;
use tiFy\Contracts\Container\ContainerInterface;
use tiFy\Contracts\View\PatternController;
use tiFy\Kernel\Container\Container;
use tiFy\Kernel\Container\ServiceProvider;
use tiFy\Kernel\Http\Request;
use tiFy\Kernel\Labels\LabelsBag;
use tiFy\Kernel\Notices\Notices;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\View\ViewEngine;

class PatternServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'db',
        'labels',
        'params',
        'notices',
        'request',
        'viewer'
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct(Container $container)
    {
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
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indice du paramètre à récupérer. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function config($key, $default = null)
    {
        return $this->getContainer()->factory()->get($key, $default);
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
        return "view.pattern.{$this->getContainer()->name()}.{$alias}";
    }

    /**
     * {@inheritdoc}
     *
     * @return PatternController|ContainerInterface
     */
    public function getContainer()
    {
        return parent::getContainer();
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
        $this->registerDb();
        $this->registerLabels();
        $this->registerParams();
        $this->registerNotices();
        $this->registerRequest();
        $this->registerViewer();
    }

    /**
     * Déclaration du controleur de base de données.
     *
     * @return void
     */
    public function registerDb()
    {
        $this->getContainer()->share($this->getFullAlias('db'), function(PatternController $pattern) {
            if ($this->config('db')) :
                return new DbItemBaseController($pattern->name());
            else :
                return null;
            endif;
        })->withArgument($this->getContainer());
    }

    /**
     * Déclaration du controleur d'intitulés.
     *
     * @return void
     */
    public function registerLabels()
    {
        $this->getContainer()->share($this->getFullAlias('labels'), function(PatternController $pattern) {
            return new LabelsBag($pattern->name(), $this->config('labels', []));
        })->withArgument($this->getContainer());
    }

    /**
     * Déclaration du controleur de paramètres.
     *
     * @return void
     */
    public function registerParams()
    {
        $this->getContainer()->share($this->getFullAlias('params'), function() {
            return new ParamsBag($this->config('params', []));
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
            return new Notices();
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
            return Request::capture();
        });
    }

    /**
     * Déclaration du controleur de gabarit d'affichage.
     *
     * @return void
     */
    public function registerViewer()
    {
        $this->getContainer()->share($this->getFullAlias('viewer'), function(PatternController $pattern) {
            $params = $this->config('viewer', []);

            if (!$params instanceof ViewEngine) :
                $viewer = new ViewEngine(
                    array_merge(
                        [
                            'directory' => pattern()->resourcesDir('/views')
                        ],
                        $params
                    )
                );
                $viewer->setController(PatternViewController::class);
                if (!$viewer->getOverrideDir()) :
                    $viewer->setOverrideDir(pattern()->resourcesDir('/views'));
                endif;
            endif;

            $viewer->set('pattern', $pattern);

            return $viewer;
        })->withArgument($this->getContainer());
    }
}