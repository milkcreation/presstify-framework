<?php

namespace tiFy\Field;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use tiFy\Contracts\Field\FieldItemInterface;
use tiFy\Contracts\Views\ViewsInterface;
use tiFy\Field\Field;
use tiFy\Field\FieldOptionsCollectionController;
use tiFy\Kernel\Tools;

abstract class AbstractFieldItem implements FieldItemInterface
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
     * @return ViewsInterface
     */
    protected $view;

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
        if (is_null($id)) :
            $id = isset($attrs['id']) ? $attrs['id'] : Str::random(32);
        endif;

        $this->id = $id;

        /** @var FieldServiceProvider $serviceProvider */
        $serviceProvider = app(FieldServiceProvider::class);
        $this->index = $serviceProvider->setInstance($this);

        $this->index ? $this->parse($attrs) : $this->boot();
    }

    /**
     * {@inheritdoc}
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

        echo is_callable($after) ? call_user_func($after) : $after;
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
        echo $this->getHtmlAttrs($this->get('attrs', []));
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
    public function defaults()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        return (string)$this->view(
            class_info($this)->getKebabName(),
            $this->all()
        );
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
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlAttrs($attrs = [], $linearized = true)
    {
        return Tools::Html()->parseAttrs($attrs, $linearized);
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
    public function getName()
    {
        return $this->get('name', '');
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return app()->resolve('field.item.field_options.collection.' . $this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->get('value', null);
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
    public function isChecked()
    {
        if ($this->get('checked') === true) :
            return true;
        endif;

        if (!$this->has('attrs.value')) :
            return false;
        endif;

        return $this->get('checked') === $this->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function options()
    {
        echo $this->getOptions();
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
                ?: 'tiFyField-' . class_info($this)->getShortName() . '-' . $this->getId()
        );

        $this->set(
            'attrs.class',
            sprintf(
                $this->get('attrs.class', '%s'),
                'tiFyField-' . class_info($this)->getShortName() .
                ' tiFyField-' . class_info($this)->getShortName() . '--' . $this->getIndex()
            )
        );

        $this->parseName();
        $this->parseValue();

        foreach($this->get('view', []) as $key => $value) :
            $this->view()->set($key, $value);
        endforeach;
    }

    /**
     * Traitement de l'attribut de configuration de la clé d'indexe de soumission du champ "name".
     *
     * @return void
     */
    protected function parseName()
    {
        if ($name = $this->get('name')) :
            $this->set('attrs.name', $name);
        endif;
    }

    /**
     * Traitement de l'attribut de configuration de la valeur de soumission du champ "value".
     *
     * @return array
     */
    protected function parseValue()
    {
        if ($value = $this->get('value')) :
            $this->set('attrs.value', $value);
        endif;
    }

    /**
     * Traitement de l'attribut de configuration de liste de selection "options".
     *
     * @return void
     */
    protected function parseOptions()
    {
        $resolved = app()->singleton(
            'field.item.field_options.collection.' . $this->getId(),
            function() {
                return new FieldOptionsCollectionController($this->get('options', []));
            }
        )->build();

        $resolved->init();
        foreach($resolved as $item) :
            if (!$item->isGroup() && in_array($item->getValue(), $this->getValue(), true)) :
                $item->push('selected', 'attrs');
            endif;
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function pull($key, $default = null)
    {
        return  Arr::pull($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function push($key, $value)
    {
        if (!$this->has($key)) :
            $this->set($key, []);
        endif;

        $arr = $this->get($key);

        if (!is_array($arr)) :
            return false;
        else :
            array_push($arr, $value);
            $this->set($key, $arr);

            return true;
        endif;
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

    /**
     * {@inheritdoc}
     */
    public function view($view = null, $data = [])
    {
        if (!$this->view) :
            $default_dir = class_info($this)->getDirname() . '/views';
            $this->view = view()
                ->setDirectory(is_dir($default_dir) ? $default_dir : null)
                ->setController(FieldView::class)
                ->set('field', $this);
        endif;

        if (func_num_args() === 0) :
            return $this->view;
        endif;

        return $this->view->make($view, $data);
    }
}