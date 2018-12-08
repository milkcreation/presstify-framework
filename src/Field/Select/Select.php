<?php

/**
 * @name Select
 * @desc Liste de selection
 * @package presstiFy
 * @namespace tiFy\Field\Select
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Field\Select;

use tiFy\Field\FieldController;

class Select extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var array $attrs Liste des propriétés de la balise HTML.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var string|array $value Attribut de configuration de la valeur initiale de soumission du champ "value".
     *      @var bool $multiple Activation de la liste de selection multiple.
     *      @var array $options Liste de selection d'éléments.
     * }
     */
    protected $attributes = [
        'before'   => '',
        'after'    => '',
        'attrs'    => [],
        'name'     => '',
        'value'    => null,
        'multiple' => false,
        'options'  => []
    ];

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        $value = $this->get('value', null);

        if (is_null($value)) :
            return null;
        endif;

        if (!is_array($value)) :
            $value = array_map('trim', explode(',', (string)$value));
        endif;

        $value = array_unique($value);

        if (!$this->get('multiple')) :
            $value = [reset($value)];
        endif;

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->parseOptions();

        if ($this->get('multiple')) :
            $this->push('attrs', 'multiple');
        endif;
    }

    /**
     * {@inheritdoc}
     */
    protected function parseName()
    {
        if ($name = $this->get('name')) :
            $this->set(
                'attrs.name',
                $this->get('multiple')
                    ? "{$name}[]" :
                    $name
            );
        endif;
    }
}