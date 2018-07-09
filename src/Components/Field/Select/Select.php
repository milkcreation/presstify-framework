<?php

/**
 * @name Select
 * @desc Liste de selection
 * @package presstiFy
 * @namespace tiFy\Components\Field\Select
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Field\Select;

use tiFy\Field\AbstractFieldItemController;

class Select extends AbstractFieldItemController
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
     * Récupération de l'attribut de configuration de la valeur initiale de soumission du champ "value".
     *
     * @return array
     */
    public function getValue()
    {
        $value = $this->get('value', null);

        if (is_null($value)) :
            return [];
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
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);
        $this->parseOptions();

        if ($this->get('multiple')) :
            array_push($this->attributes['attrs'], 'multiple');
        endif;
    }

    /**
     * Traitement de l'attribut de configuration de la qualification de soumission du champ "name".
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return array
     */
    protected function parseName($attrs = [])
    {
        if (isset($this->attributes['name'])) :
            $this->attributes['attrs']['name'] = !empty($this->attributes['multiple']) ? "{$this->attributes['name']}[]" : $this->attributes['name'];
        endif;
    }
}