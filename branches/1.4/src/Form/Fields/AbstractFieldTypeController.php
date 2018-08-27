<?php

namespace tiFy\Form\Fields;

use tiFy\Form\AbstractCommonDependency;
use tiFy\Form\Fields\FieldItemController;
use tiFy\Form\Forms\FormItemController;

abstract class AbstractFieldTypeController extends AbstractCommonDependency implements FieldTypeControllerInterface
{
    /**
     * Nom de qualification du type de champ.
     * @var string
     */
    protected $name;

    /**
     * Classe de rappel du champ de formualire.
     * @var Field
     */
    protected $field;

    /**
     * Liste des classes de rappel active
     * @var array
     */
    protected $callbacks = [];

    /**
     * Liste des attributs de configuration par defaut.
     * @var array
     */
    protected $defaultOptions = [];

    /**
     * Liste des attributs des attributs HTML par defaut.
     * @var array
     */
    protected $defaultHtmlAttrs = [];

    /**
     * Liste des propriétés supportées.
     * @var array
     */
    protected $support = [];

    /**
     * CONSTRUCTEUR
     */
    public function __construct()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function make($name, $field)
    {
        $this->name = $name;
        $this->field = $field;
        $this->form = $field->getForm();

        $this->parseCallbacks();
        $this->parseDefaultOptions();
        $this->parseDefaultHtmLAttrs();
        $this->parseDefaultSupport();

        if (method_exists($this, 'boot')) :
            call_user_func([$this, 'boot']);
        endif;
    }

    /**
     * Récupération de la classe de rappel du champ associé.
     *
     * @return FieldItemController
     */
    public function relField()
    {
        return $this->field;
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
    public function parseCallbacks()
    {
        foreach ($this->callbacks as $hookname => $args) :
            if (is_callable($args)) :
                $this->getForm()
                    ->callbacks()
                    ->set(
                        $hookname,
                        $args
                    );
            elseif (isset($args['function']) && is_callable($args['function'])) :
                $args = array_merge(
                    ['order' => 10],
                    $args
                );
                $this->getForm()
                    ->callbacks()
                    ->set(
                        $hookname,
                        $args['function'],
                        $args['order']
                    );
            endif;
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function parseDefaultOptions()
    {
        return $this->relField()->parseDefaultOptions($this->defaultOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function parseDefaultHtmlAttrs()
    {
        return $this->relField()->parseDefaultHtmlAttrs($this->defaultHtmlAttrs);
    }

    /**
     * {@inheritdoc}
     */
    public function parseDefaultSupport()
    {
        return $this->relField()->parseDefaultSupport($this->support);
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlAttrs()
    {
        return $this->relField()->getHtmlAttrs();
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlAttr($key, $default = null)
    {
        return $this->relField()->getHtmlAttr($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->relField()->getOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($key, $default = null)
    {
        return $this->relField()->getOption($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function support($key)
    {
        return $this->relField()->support($key);
    }

    /**
     * {@inheritdoc}
     */
    public function displayHtmlAttrs()
    {
        return $this->parseHtmlAttrs($this->relField()->getHtmlAttrs());
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        $output = "";

        $output .= $this->render();

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function render();
}