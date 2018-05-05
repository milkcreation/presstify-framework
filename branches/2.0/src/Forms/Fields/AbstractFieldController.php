<?php

namespace tiFy\Forms\Fields;

use tiFy\Apps\AppTrait;
use tiFy\Forms\Fields;
use tiFy\Forms\Form\Field;
use tiFy\Forms\Form\Form;
use tiFy\Forms\Form\Helpers;

abstract class AbstractFieldController implements FieldControllerInterface
{
    use AppTrait;

    /**
     * Nom de qualification du type de champ.
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $supports = [];

    /**
     * Liste des options.
     * @var array
     */
    private $options = [];

    /**
     * Liste des classes de rappel active
     * @var array
     */
    public $callbacks = [];

    /**
     * Classe de rappel du formulaire.
     * @var Form
     */
    protected $form;

    /**
     * Classe de rappel du champ de formualire.
     * @var Field
     */
    protected $field;

    /**
     * {@inheritdoc}
     */
    public function make($name, $field, $attrs = [])
    {
        $this->name = $name;
        $this->field = $field;
        $this->form = $form->form();

        $this->parseCallbacks();
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
    public function field()
    {
        return $this->field;
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
                    ->setFieldType(
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
                    ->setFieldType(
                        $hookname,
                        $this->getName(),
                        $args['function'],
                        $args['order']
                    );
            endif;
        endforeach;
    }

    /**
     * Vérification de permission de support.
     *
     * @return bool
     */
    final public function support($support)
    {
        return in_array($support, $this->supports);
    }

    /** == Définition des options == **/
    public function initOptions($options)
    {
        $this->Options = Helpers::parseArgs($options, $this->Defaults);
    }


    /** == Récupération des options == **/
    final public function getOptions()
    {
        return $this->Options;
    }

    /** == Récupération d'une option == **/
    final public function getOption($option, $default = '')
    {
        if (isset($this->Options[$option])) {
            return $this->Options[$option];
        }

        return $default;
    }

    /** == Récupération d'une option == **/
    final public function setOption($option, $value)
    {
        return $this->Options[$option] = $value;
    }

    /** == Identifiant HTML de l'interface de saisie == **/
    public function getInputID()
    {
        return "tiFyForm-FieldInput--" . $this->field()
                                              ->formID() . "_" . $this->field()
                                                                      ->getSlug();
    }

    /** == Classes HTML de l'interface de saisie == **/
    public function getInputClasses()
    {
        $classes = [];

        if ($this->field()->getAttr('input_class')) {
            $classes[] = $this->field()->getAttr('input_class');
        }
        $classes[] = "tiFyForm-FieldInput";
        $classes[] = "tiFyForm-FieldInput--" . $this->getID();
        $classes[] = "tiFyForm-FieldInput--" . $this->field()->getSlug();

        return $this->form()->factory()->fieldClasses($this->field(), $classes);
    }

    /** == Texte d'aide de l'interface de saisie == **/
    public function getInputPlaceholder()
    {
        if ( ! $placeholder = $this->field()->getAttr('placeholder')) {
            return;
        }

        if (is_bool($placeholder)) :
            $placeholder = $this->field()->getAttr('label');
        endif;

        return (string)$placeholder;
    }

    /** == Attributs HTML du champs de saisie == **/
    public function getInputHtmlAttrs()
    {
        $attrs = [];
        foreach ($this->HtmlAttrs as $attr) :
            $attrs[] = HtmlAttrs::getValue($attr, $this->field()->getAttr($attr));
        endforeach;

        if(! empty($attrs))
            return implode(" ", $attrs);
    }

    /** == Attributs tabindex de navigation au clavier == **/
    public function getTabIndex()
    {
        return "tabindex=\"{$this->field()->getTabIndex()}\"";
    }

    /* = AFFICHAGE = */
    /** == == **/
    final public function _display()
    {
        $output = "";
        // Affichage de l'intitulé
        if ($this->isSupport('label')) :
            $output .= $this->displayLabel();
        endif;

        // Pré-affichage
        $output .= $this->form()
                        ->factory()
                        ->fieldBefore($this->field(),
                            $this->field()->getAttr('before'));

        $output .= $this->display();

        // Post-affichage
        $output .= $this->form()
                        ->factory()
                        ->fieldAfter($this->field(),
                            $this->field()->getAttr('after'));

        return $output;
    }

    /** == Affichage == **/
    abstract protected function display();

    /** == Affichage de l'intitulé de champ == **/
    public function displayLabel()
    {
        $input_id = $this->getInputID();
        $class    = [];
        if ($this->field()->getAttr('label_class')) {
            $class[] = $this->field()->getAttr('label_class');
        }
        $class[]  = "tiFyForm-FieldLabel";
        $class[]  = "tiFyForm-FieldLabel--" . $this->getID();
        $class[]  = "tiFyForm-FieldLabel--" . $this->field()->getSlug();
        $label    = $this->field()->getLabel();
        $required = ($this->field()
                          ->getRequired('tagged')) ? "<span class=\"tiFyForm-FieldRequiredTag\">*</span>" : '';

        return $this->form()
                    ->factory()
                    ->fieldLabel($this->field(), $input_id, join(' ', $class),
                        $label, $required);
    }
}