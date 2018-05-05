<?php

namespace tiFy\Forms\Addons;

use tiFy\Apps\AppTrait;
use tiFy\Forms\Addons;
use tiFy\Forms\Form\Form;
use tiFy\Forms\Form\Helpers;

abstract class AbstractAddonController implements AddonControllerInterface
{
    use AppTrait;

    /**
     * Nom de qualification de l'addon.
     * @var string
     */
    protected $name;

    /**
     * Liste des options du formulaire associé.
     * @var array
     */
    protected $formOptions = [];

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
     * Classe de rappel du formulaire.
     * @var Form
     */
    protected $form;

    /**
     * {@inheritdoc}
     */
    public function make($name, $form, $attrs = [])
    {
        $this->name = $name;
        $this->form = $form;
        $this->formAttrs = $this->parseFormOptions($attrs);
        $this->parseCallbacks();

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
    public function form()
    {
        return $this->form;
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return $this->form->fields();
    }

    /**
     * {@inheritdoc}
     */
    public function getFormOption($key, $default = null)
    {
        return Arr::get($this->formOptions, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function parseFormOptions($attrs = [])
    {
        return Helpers::parseArgs($attrs, $this->form_options);
    }

    /**
     * {@inheritdoc}
     */
    public function parseCallbacks()
    {
        foreach ($this->callbacks as $hookname => $args) :
            if (is_callable($args)) :
                $this->form()
                    ->callbacks()
                    ->setAddons(
                        $hookname,
                        $this->getName(),
                        $args
                    );
            elseif (isset($args['function']) && is_callable($args['function'])) :
                $args = array_merge(
                    ['order' => 10],
                    $args
                );
                $this->form()
                    ->callbacks()
                    ->setAddons(
                        $hookname,
                        $this->getName(),
                        $args['function'],
                        $args['order']
                    );
            endif;
        endforeach;
    }

    /** == Initialisation du formulaire courant == **/
    final public function setField($field, $attrs = [])
    {
        // Définition des attributs de champs
        $attrs                                = Helpers::parseArgs($attrs,
            $this->default_field_options);
        $this->FieldsAttrs[$field->getSlug()] = $attrs;

        return $attrs;
    }

    /** == Récupération des attributs de formulaire == **/
    final public function getFieldAttr($field, $attr, $default = '')
    {
        if ( ! is_object($field)) {
            $field = $this->Form->getField($field);
        }

        if (isset($this->FieldsAttrs[$field->getSlug()][$attr])) {
            return $this->FieldsAttrs[$field->getSlug()][$attr];
        }

        return $default;
    }
}