<?php

namespace tiFy\Apps\Layout;

use Illuminate\Support\Arr;
use tiFy\Apps\Layout\Db\DbInterface;
use tiFy\Apps\Layout\Labels\LabelsInterface;
use tiFy\Apps\Layout\LayoutViewInterface;
use tiFy\Apps\Layout\LayoutServiceProvider;
use tiFy\Apps\Layout\Notices\NoticesInterface;
use tiFy\Apps\Layout\Params\ParamsInterface;
use tiFy\Apps\Layout\Request\RequestInterface;
use tiFy\Apps\Container\Container;

abstract class AbstractLayoutBaseController extends Container implements LayoutInterface
{
    /**
     * Classe de rappel du controleur de vue associé.
     * @var LayoutViewInterface
     */
    protected $app;

    /**
     * Nom de qualification du controleur.
     * @var string
     */
    protected $name;

    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes = [];

    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $providers = [
        LayoutServiceProvider::class
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification du controleur.
     * @param array $attrs Liste des attributs de configuration.
     * @param LayoutViewInterface $app Classe de rappel du controleur de vue associé.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], $app)
    {
        $this->name = $name;
        $this->app = $app;

        $this->attributes = array_merge(
            $this->attributes,
            $attrs
        );

        parent::__construct();

        if (method_exists($this, 'boot')) :
            $this->boot();
        endif;
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $this->process();
        $this->prepare();
    }

    /**
     * {@inheritdoc}
     */
    public function db()
    {
        return $this->resolve(DbInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel($key, $default = '')
    {
        return $this->labels()->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return Arr::has($this->attributes, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function labels()
    {
        return $this->resolve(LabelsInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function notices()
    {
        return $this->resolve(NoticesInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function param($key, $default = null)
    {
        return $this->params()->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function params()
    {
        return $this->resolve(ParamsInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        return Arr::set($this->attributes, $key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function request()
    {
        return $this->resolve(RequestInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        _e('Aucun contenu à afficher', 'tify');
    }

    /**
     * {@inheritdoc}
     */
    public function view()
    {
        return $this->app;
    }
}