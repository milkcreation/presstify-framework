<?php

namespace tiFy\Form\Addons;

use Illuminate\Support\Arr;
use tiFy\Form\Addons\AddonsController;
use tiFy\Form\AbstractCommonDependency;
use tiFy\Form\Fields\FieldItemController;
use tiFy\Form\Forms\FormItemController;

abstract class AbstractAddonController extends AbstractCommonDependency implements AddonControllerInterface
{
    /**
     * Nom de qualification de l'addon.
     * @var string
     */
    protected $name;

    /**
     * Liste des options par défaut du formulaire associé.
     * @var array
     */
    protected $defaultFormOptions = [];

    /**
     * Liste des options par défaut des champs du formulaire associé.
     * @var array
     */
    protected $defaultFieldOptions = [];

    /**
     * Liste des méthodes de rappel de court-circuitage.
     * @var array
     */
    protected $callbacks = [];

    /**
     * CONSTRUCTEUR.
     *
     * @void
     */
    public function __construct()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function make($name, $form, $attrs = [])
    {
        $this->name = $name;
        $this->form = $form;

        $this->parseCallbacks();
        $this->parseDefaultFormOptions();

        if (method_exists($this, 'boot')) :
            call_user_func([$this, 'boot']);
        endif;
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
    public function parseDefaultFormOptions()
    {
        return $this->getForm()->parseDefaultAddonOptions($this->getName(), $this->defaultFormOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormOption($key, $default = null)
    {
        return $this->getForm()->getAddonOption($this->getName(), $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function parseDefaultFieldOptions($field)
    {
        return $field->parseDefaultAddonOptions($this->getName(), $this->defaultFieldOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldOption($field, $key, $default = '')
    {
        return $field->getAddonOption($this->getName(), $key, $default);
    }
}