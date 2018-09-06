<?php

namespace tiFy\Partial;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use tiFy\Contracts\Partial\PartialItemInterface;
use tiFy\Kernel\Tools;
use tiFy\Kernel\Templates\EngineInterface;
use tiFy\Partial\PartialServiceProvider;
use tiFy\Partial\TemplateController;

abstract class AbstractPartialItem implements PartialItemInterface
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
     * @return EngineInterface
     */
    protected $view;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct($id = null, $attrs = [])
    {
        if (is_null($id)) :
            $id = Str::random(32);
        elseif(is_array($id)) :
            $attrs = $id;
            $id = isset($attrs['id']) ? $attrs['id'] : Str::random(32);
        endif;

        $this->id = $id;

        /** @var PartialServiceProvider $serviceProvider */
        $serviceProvider = app(PartialServiceProvider::class);
        $this->index = $serviceProvider->setInstance($this);

        $this->index ? $this->parse($attrs) : $this->boot();
    }

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->display();
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
    public function all()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function attrs()
    {
        echo Tools::Html()->parseAttrs($this->get('attrs', []), true);
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
     * Liste des attributs de configuration par défaut.
     *
     * @return array
     */
    public function defaults()
    {
        return [];
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
        return $this->getView()->render(
            class_info($this)->getKebabName(),
            $this->all()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {

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
    public function getView($view = null, $data = [])
    {
        if (!$this->view) :
            $default_dir = class_info($this)->getDirname() . '/views';
            $this->view = view()
                ->setDirectory(is_dir($default_dir) ? $default_dir : null)
                ->setController(PartialViewTemplate::class)
                ->set('partial', $this);
        endif;

        if (func_num_args() === 0) :
            return $this->view;
        endif;

        return $this->view->make($view, $data);
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
    public function isCallable($var)
    {
        return Tools::Functions()->isCallable($var);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->attributes = array_merge(
            $this->defaults(),
            $this->attributes,
            $attrs
        );

        $this->parseDefaults();
    }

    /**
     * Traitement de la liste des attributs par défaut.
     *
     * @return void
     */
    protected function parseDefaults()
    {
        $this->set(
            'attrs.id',
                $this->get('attrs.id', '')
                ?: 'tiFyPartial-' . class_info($this)->getShortName() . '-' . $this->getId()
        );

        $this->set(
            'attrs.class',
            sprintf(
                $this->get('attrs.class', '%s'),
                'tiFyPartial-' . class_info($this)->getShortName() .
                ' tiFyPartial-' . class_info($this)->getShortName() . '--' . $this->getIndex()
            )
        );

        foreach($this->get('view', []) as $key => $value) :
            $this->getView()->set($key, $value);
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function pull($key, $default = null)
    {
        return Arr::pull($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function values()
    {
        return array_values($this->attributes);
    }
}