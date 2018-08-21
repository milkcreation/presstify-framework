<?php

namespace tiFy\App\Templates;

use App\App;
use Illuminate\Support\Arr;
use League\Plates\Template\Template;
use tiFy\App\AppInterface;
use tiFy\App\Templates\Engine;

class TemplateBaseController extends Template implements TemplateControllerInterface
{
    /**
     * Classe de rappel de l'application associée.
     * @var AppInterface
     */
    protected $app;

    /**
     * Liste des variables passées en argument dans le controleur.
     * @var array
     */
    protected $args = [];

    /**
     * Instance of the template engine.
     * @var Engine
     */
    protected $engine;

    /**
     * CONSTRUCTEUR.
     *
     * @param Engine $engine
     * @param string $name
     * @param array $args Liste des variables passées en argument
     * @param AppInterface Classe de rappel de l'application associée.
     *
     * @return void
     */
    public function __construct(Engine $engine, $name, $args = [], AppInterface $app)
    {
        $this->app = $app;
        $this->args = $args;

        parent::__construct($engine, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = '')
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getArg($key, $default = null)
    {
        return Arr::get($this->args, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return Arr::has($this->data, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function htmlAttrs($attrs, $linearized = true)
    {
        $html_attrs = [];
        foreach ($attrs as $k => $v) :
            if (is_array($v)) :
                $v = rawurlencode(json_encode($v));
            endif;
            if (is_numeric($k)) :
                $html_attrs[]= "{$v}";
            else :
                $html_attrs[]= "{$k}=\"{$v}\"";
            endif;
        endforeach;

        return $linearized ? implode(' ', $html_attrs) : $html_attrs;
    }

    /**
     * {@inheritdoc}
     */
    public function reset($name)
    {
        $this->start($name); $this->stop();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function share($datas)
    {
        return $this->engine->addData($datas);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        return Arr::set($this->data, $key, $value);
    }
}