<?php

namespace tiFy\Partial;

use Closure;
use Illuminate\Support\Str;
use tiFy\Contracts\Partial\PartialFactory as PartialFactoryContract;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Support\HtmlAttrs;
use tiFy\Support\ParamsBag;

abstract class PartialFactory extends ParamsBag implements PartialFactoryContract
{
    /**
     * Identifiant de qualification du champ.
     * @var string
     */
    protected $id = '';

    /**
     * Compte de l'indice de l'instance courante.
     * @var int
     */
    protected $index = 0;

    /**
     * Instance du moteur de gabarits d'affichage.
     * @return ViewEngine
     */
    protected $viewer;

    /**
     * Répertoire de stockage par défaut des gabarits d'affichage.
     * @var resource
     */
    protected $viewer_dir;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $id Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($id = null, $attrs = [])
    {
        $id = $id ?? Str::random(32);

        $this->id = $id;
        $this->index = partial()->index($this);
        $this->index ? $this->set($attrs)->parse() : $this->boot();
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return (string)$this->display();
    }

    /**
     * @inheritdoc
     */
    public function after()
    {
        $after = $this->get('after', '');

        echo $after instanceof Closure ? call_user_func($after) : $after;
    }

    /**
     * @inheritdoc
     */
    public function attrs()
    {
        echo HtmlAttrs::createFromAttrs($this->get('attrs', []));
    }

    /**
     * @inheritdoc
     */
    public function before()
    {
        $before = $this->get('before', '');

        echo $before instanceof Closure ? call_user_func($before) : $before;
    }

    /**
     * @inheritdoc
     */
    public function boot()
    {

    }

    /**
     * @inheritdoc
     */
    public function content()
    {
        $content = $this->get('content', '');

        echo $content instanceof Closure ? call_user_func($content) : $content;
    }

    /**
     * @inheritdoc
     */
    public function display()
    {
        return $this->viewer(class_info($this)->getKebabName(), $this->all());
    }

    /**
     * @inheritdoc
     */
    public function enqueue_scripts()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        parent::parse();

        $this->parseDefaults();
    }

    /**
     * @inheritdoc
     */
    public function parseDefaults()
    {
        if (!$this->get('attrs.id')) {
            $this->pull('attrs.id');
        }

        $default_class = 'tiFyPartial-' . class_info($this)->getShortName() .
            ' tiFyPartial-' . class_info($this)->getShortName() . '--' . $this->getIndex();
        if (!$this->has('attrs.class')) {
            $this->set(
                'attrs.class',
                $default_class
            );
        } else {
            $this->set(
                'attrs.class',
                sprintf(
                    $this->get('attrs.class', ''),
                    $default_class
                )
            );
        }
        if (!$this->get('attrs.class')) {
            $this->pull('attrs.class');
        }

        foreach($this->get('view', []) as $key => $value) {
            $this->viewer()->set($key, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function viewer($view = null, $data = [])
    {
        if (is_null($this->viewer)) :
            $this->viewer = app()->get('partial.viewer', [$this]);
        endif;

        if (func_num_args() === 0) :
            return $this->viewer;
        endif;

        return $this->viewer->make("_override::{$view}", $data);
    }
}