<?php

namespace tiFy\Partial;

use Illuminate\Support\Str;
use tiFy\Contracts\Partial\PartialController as PartialControllerContract;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\Kernel\Tools;
use tiFy\Support\HtmlAttrs;

abstract class PartialController extends ParamsBag implements PartialControllerContract
{
    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes = [];

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
        $this->viewer_dir = partial()->resourcesDir('/views/'. class_info($this)->getKebabName());

        $this->index = partial()->index($this);
        $this->index ? parent::__construct($attrs) : $this->boot();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->display();
    }

    /**
     * {@inheritdoc}
     */
    public function after()
    {
        $after = $this->get('after', '');

        echo $this->isCallable($after) ? call_user_func($after) : $after;
    }

    /**
     * {@inheritdoc}
     */
    public function attrs()
    {
        echo HtmlAttrs::createFromAttrs($this->get('attrs', []));
    }

    /**
     * {@inheritdoc}
     */
    public function before()
    {
        $before = $this->get('before', '');

        echo $this->isCallable($before) ? call_user_func($before) : $before;
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
    public function content()
    {
        $content = $this->get('content', '');

        echo $this->isCallable($content) ? call_user_func($content) : $content;
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        return $this->viewer(class_info($this)->getKebabName(), $this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function isCallable($var)
    {
        return Tools::Functions()->isCallable($var);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->parseDefaults();
    }

    /**
     * {@inheritdoc}
     */
    public function parseDefaults()
    {
        $default_class = 'tiFyPartial-' . class_info($this)->getShortName() .
            ' tiFyPartial-' . class_info($this)->getShortName() . '--' . $this->getIndex();
        if (!$this->has('attrs.class')) :
            $this->set(
                'attrs.class',
                $default_class
            );
        else :
            $this->set(
                'attrs.class',
                sprintf(
                    $this->get('attrs.class', ''),
                    $default_class
                )
            );
        endif;
        if (!$this->get('attrs.class')) :
            $this->pull('attrs.class');
        endif;

        foreach($this->get('view', []) as $key => $value) :
            $this->viewer()->set($key, $value);
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function viewer($view = null, $data = [])
    {
        if (!$this->viewer) :
            $this->viewer = view()
                ->setDirectory(is_dir($this->viewer_dir) ? $this->viewer_dir : null)
                ->setController(PartialView::class)
                ->setOverrideDir(
                    (($override_dir = $this->get('viewer.override_dir')) && is_dir($override_dir))
                        ? $override_dir
                        : (is_dir($this->viewer_dir) ? $this->viewer_dir : __DIR__)
                )
                ->set('partial', $this);
        endif;

        if (func_num_args() === 0) :
            return $this->viewer;
        endif;

        return $this->viewer->make("_override::{$view}", $data);
    }
}