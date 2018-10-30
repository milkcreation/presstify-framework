<?php

namespace tiFy\Field;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use tiFy\Contracts\Field\FieldController as FieldControllerInterface;
use tiFy\Contracts\Field\FieldManager;
use tiFy\Contracts\Views\ViewsInterface;
use tiFy\Field\FieldOptionsCollectionController;
use tiFy\Kernel\Parameters\AbstractParametersBag;
use tiFy\Kernel\Tools;

abstract class FieldController extends AbstractParametersBag implements FieldControllerInterface
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
    protected $viewer;

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

        /** @var FieldManager $field */
        $field = app('field');
        $this->index = $field->index($this);
        $this->index ? $this->parse($attrs) : $this->boot();
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

        echo is_callable($after) ? call_user_func($after) : $after;
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
    public function display()
    {
        return $this->viewer(
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
        parent::parse($attrs);

        $this->parseDefaults();
    }

    /**
     * Traitement de la liste des attributs par défaut.
     *
     * @return void
     */
    protected function parseDefaults()
    {
        if (!$this->has('attrs.id', '')) :
            $this->set('attrs.id', 'tiFyField-' . class_info($this)->getShortName() . '-' . $this->getId());
        endif;

        $default_class = 'tiFyField-' . class_info($this)->getShortName() .
            ' tiFyField-' . class_info($this)->getShortName() . '--' . $this->getIndex();
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

        $this->parseName();
        $this->parseValue();

        foreach($this->get('view', []) as $key => $value) :
            $this->viewer()->set($key, $value);
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
            if (!$item->isGroup() && in_array($item->getValue(), (array)$this->getValue(), true)) :
                $item->push('attrs', 'selected');
            endif;
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function viewer($view = null, $data = [])
    {
        if (!$this->viewer) :
            $cinfo = class_info($this);
            $default_dir = $cinfo->getDirname() . '/views';
            $this->viewer = view()
                ->setDirectory(is_dir($default_dir) ? $default_dir : null)
                ->setController(FieldView::class)
                ->setOverrideDir(
                    (($override_dir = $this->get('viewer.override_dir')) && is_dir($override_dir))
                        ? $override_dir
                        : (is_dir($default_dir) ? $default_dir : $cinfo->getDirname())
                )
                ->set('field', $this);
        endif;

        if (func_num_args() === 0) :
            return $this->viewer;
        endif;

        return $this->viewer->make("_override::{$view}", $data);
    }
}