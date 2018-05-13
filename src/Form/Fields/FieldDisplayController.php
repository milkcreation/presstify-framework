<?php

namespace tiFy\Form\Fields;

use Illuminate\Support\Arr;
use tiFy\Form\AbstractCommonDependency;
use tiFy\Form\Fields\FieldItemController;
use tiFy\Form\Fields\FieldTypesController;
use tiFy\Form\Fields\AbstractFieldTypeController;
use tiFy\Form\Forms\FormItemController;
use tiFy\Partial\Partial;

class FieldDisplayController extends AbstractCommonDependency
{
    /**
     * Liste des attributs de configuration.
     * @return array
     */
    protected $attributes = [];

    /**
     * Classe de rappel du controleur du champ associé.
     * @var FieldItemController
     */
    protected $field;

    /**
     * CONSTRUCTEUR.
     *
     * @param FieldItemController $field Classe de rappel du controleur du champ associé.
     *
     * @return void
     */
    public function __construct(FieldItemController $field)
    {
        parent::__construct($field->getForm());

        $this->field = $field;

        $this->parse();
    }

    /**
     * Récupération de la classe de rappel du controleur du champ associé.
     *
     * @return FieldItemController
     */
    public function relField()
    {
        return $this->field;
    }

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut à récupérer.
     * @param mixed $defaul Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     *
     */
    public function getAttr($key, $default = null)
    {
        return $this->relField()->get($key, $default);
    }

    /**
     *
     */
    public function getIndex()
    {
        return $this->relField()->getIndex();
    }

    /**
     *
     */
    public function getName()
    {
        return $this->relField()->getName();
    }

    /**
     *
     */
    public function getSlug()
    {
        return $this->relField()->getSlug();
    }

    public function support($prop)
    {
        return $this->relField()->support($prop);
    }

    /**
     * Récupération de la liste des données d'erreur du champ.
     *
     * @return array
     */
    public function queryErrors($args = [])
    {
        return parent::queryErrors(
            array_merge(
                $args,
                [
                    'type' => 'field',
                    'slug' => $this->relField()->getSlug(),
                ]
            )
        );
    }

    /**
     * Affichage du champ.
     *
     * @return string
     */
    public function render()
    {
        $output = "";

        // Court-circuitage post-affichage
        $this->call('form_before_display', [&$output, $this->getForm()]);

        $output .= $this->get('wrapper');

        // Court-circuitage post-affichage
        $this->call('form_after_display', [&$output, $this->getForm()]);

        return $output;
    }

    /**
     * Encapsulation du contenu.
     *
     * @return string
     */
    public function wrapper()
    {
        $output = "";

        $output .= $this->get('label', '');

        $output .= $this->relField()->getFieldTypeController()->display();

        return $output;
    }

    /**
     * Traitement de la liste des attributs de configuration.
     * @todo Fractionner le traitement pour une meilleur lisibilité
     *
     * @return array
     */
    public function parse()
    {
        // Conteneur
        if ($this->support('wrapper') && ($wrapper_attrs = $this->getAttr('wrapper'))) :
            if (!is_array($wrapper_attrs)) :
                $wrapper_attrs = [
                    'attrs' => [
                        'id'    => '%s',
                        'class' => '%s',
                    ],
                ];
            endif;

            $wrapper_attrs = array_merge(['tag' => 'div'], $wrapper_attrs);

            Arr::set(
                $wrapper_attrs,
                'attrs.id',
                sprintf(
                    Arr::get($wrapper_attrs, 'attrs.id'),
                    'tiFyForm-FieldWrapper--' . $this->getIndex()
                )
            );

            Arr::set(
                $wrapper_attrs,
                'attrs.class',
                sprintf(
                    Arr::get($wrapper_attrs, 'attrs.class'),
                    'tiFyForm-FieldWrapper' .
                    ' tiFyForm-FieldWrapper--' . $this->getAttr('type') .
                    ' tiFyForm-FieldWrapper--' . $this->getSlug() .
                    ($this->queryErrors() ? ' tiFyForm-FieldWrapper--error' : '') .
                    ($this->relField()->getRequiredAttr('tag') ? ' tiFyForm-FieldWrapper--required' : '')
                )
            );

            $wrapper_attrs['content'] = [$this, 'wrapper'];

            $wrapper = Partial::Tag($wrapper_attrs);
        else :
            $wrapper = $this->wrapper();
        endif;

        // Intitulé
        if ($this->support('label') && ($label_attrs = $this->getAttr('label'))) :
            if (is_string($label_attrs)) :
                $text = $label_attrs;
            elseif (is_bool($label_attrs)) :
                $text = $this->relField()->getTitle();
            else :
                $text = $this->getSlug();
            endif;

            if (!is_array($label_attrs)) :
                $label_attrs = [
                    'attrs' => [
                        'id'    => '%s',
                        'class' => '%s',
                    ],
                ];
            endif;

            $label_attrs = array_merge(['tag' => 'label'], $label_attrs);

            Arr::set(
                $label_attrs,
                'attrs.id',
                sprintf(
                    Arr::get($label_attrs, 'attrs.id'),
                    'tiFyForm-FieldLabel--' . $this->getIndex()
                )
            );

            Arr::set(
                $label_attrs,
                'attrs.class',
                sprintf(
                    Arr::get($label_attrs, 'attrs.class'),
                    'tiFyForm-FieldLabel' .
                    ' tiFyForm-FieldLabel--' . $this->getAttr('type') .
                    ' tiFyForm-FieldLabel--' . $this->getSlug() .
                    ($this->queryErrors() ? ' tiFyForm-FieldLabel--error' : '') .
                    ($this->relField()->getRequiredAttr('tag') ? ' tiFyForm-FieldLabel--required' : '')
                )
            );

            if ($input_id = $this->relField()->getHtmlAttr('id')) :
                Arr::set(
                    $label_attrs,
                    'attrs.for',
                    $input_id
                );
            endif;

            $label_attrs['content'] = isset($label_attrs['content']) ? $label_attrs['content'] : $text;

            if ($tag = $this->relField()->getRequiredAttr('tag')) :
                $label_attrs['content'] .= is_string($tag) ? $tag : "<span class=\"tiFyForm-FieldRequiredTag\">*</span>";
            endif;

            $label = Partial::Tag($label_attrs);
        else :
            $label = '';
        endif;

        $this->attributes = compact('wrapper', 'label');
    }

    /**
     * Récupération de l'affichage du champ depuis l'instance.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}