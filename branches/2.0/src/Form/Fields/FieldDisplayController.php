<?php

namespace tiFy\Form\Fields;

use Illuminate\Support\Arr;
use tiFy\Form\AbstractCommonDependency;
use tiFy\Form\Fields\FieldItemController;
use tiFy\Form\Fields\FieldTypesController;
use tiFy\Form\Fields\AbstractFieldTypeController;
use tiFy\Form\Forms\FormItemController;

class FieldDisplayController extends AbstractCommonDependency
{
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
     * Récupération des attribut de configuration de l'encapsuleur de champ.
     *
     * @return array
     */
    public function getWrapperAttrs()
    {
        if (!$wrapper = $this->get('wrapper')) :
            return [];
        endif;


        $id = 'tiFyForm-FieldWrapper--' . $this->getIndex();
        $classes = [];
        $classes[] = 'tiFyForm-FieldWrapper';
        $classes[] = 'tiFyForm-FieldWrapper--' . $this->get('type');
        $classes[] = 'tiFyForm-FieldWrapper--' . $this->getSlug();

        if ($this->queryErrors()) :
            $classes[] = 'tiFyForm-FieldWrapper--error';
        endif;
        if ($this->getRequiredAttr('tag')) :
            $classes[] = 'tiFyForm-FieldWrapper--required';
        endif;
        $class = join(' ', $classes);
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
                    'slug' => $this->getSlug()
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

        $output .= $this->relField()->getFieldTypeController()->display();

        return $output;
    }

    /** == Affichage de l'intitulé de champ == **/
    public function displayLabel()
    {
        $input_id = $this->getInputID();
        $class    = [];
        if ($this->field()->get('label_class')) {
            $class[] = $this->field()->get('label_class');
        }
        $class[]  = "tiFyForm-FieldLabel";
        $class[]  = "tiFyForm-FieldLabel--" . $this->getName();
        $class[]  = "tiFyForm-FieldLabel--" . $this->field()->getSlug();
        $label    = $this->field()->getLabel();
        $required = ($this->field()
            ->getRequired('tagged')) ? "<span class=\"tiFyForm-FieldRequiredTag\">*</span>" : '';

        return $this->getForm()
            ->controller()
            ->fieldLabel(
                $this->field(),
                $input_id,
                join(' ', $class),
                $label, $required
            );
    }


    public function __toString()
    {
        return $this->render();
    }
}