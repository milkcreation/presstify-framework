<?php

namespace tiFy\Layout\Base;

use tiFy\Contracts\Layout\LayoutDisplayInterface;
use tiFy\Contracts\Layout\LayoutFactoryInterface;
use tiFy\Contracts\Views\ViewsInterface;
use tiFy\Kernel\Container\Container;

abstract class AbstractBaseController extends Container implements LayoutDisplayInterface
{
    /**
     * Instance de la fabrique de disposition associée.
     * @var LayoutFactoryInterface
     */
    protected $factory;

    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [
        ServiceProvider::class
    ];

    /**
     * Instance du moteur de gabarits d'affichage.
     * @return ViewsInterface
     */
    protected $viewer;

    /**
     * CONSTRUCTEUR.
     *
     * @param LayoutFactoryInterface $factory Instance de la fabrique de disposition associée.
     *
     * @return void
     */
    public function __construct(LayoutFactoryInterface $factory)
    {
        $this->factory = $factory;

        parent::__construct();

        $this->boot();
    }

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->render();
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->factory()->all();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function db()
    {
        return $this->resolve('db', [$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return $this->factory()->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function factory()
    {
        return $this->factory;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return $this->factory()->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function label($key = null, $default = '')
    {
        $labels = $this->resolve('labels', [$this]);

        if (is_null($key)) :
            return $labels;
        endif;

        return $labels->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $this->process();
        $this->prepare();
    }


    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return $this->factory()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function notices()
    {
        return $this->resolve('notices', [$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function param($key = null, $default = null)
    {
        $params = $this->resolve('params', [$this]);

        if (is_null($key)) :
            return $params;
        endif;

        return $params->get($key, $default);
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
    public function set($key, $value)
    {
        return $this->factory()->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function request()
    {
        return $this->resolve('request', [$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return __('Aucun contenu à afficher', 'tify');
    }

    /**
     * {@inheritdoc}
     */
    public function viewer($view = null, $data = [])
    {
        if (!$this->viewer) :
            $cinfo = class_info($this);

            $default_dir = dirname(__DIR__) . '/Resources/views/' . $cinfo->getKebabName();
            $this->viewer = view()
                ->setDirectory(is_dir($default_dir) ? $default_dir : null)
                ->setController(ViewController::class)
                ->setOverrideDir(
                    (($override_dir = $this->get('viewer.override_dir')) && is_dir($override_dir))
                        ? $override_dir
                        : (is_dir($default_dir) ? $default_dir : $cinfo->getDirname())
                )
                ->set('layout', $this);
        endif;

        if (func_num_args() === 0) :
            return $this->viewer;
        endif;

        return $this->viewer->make("_override::{$view}", $data);
    }
}