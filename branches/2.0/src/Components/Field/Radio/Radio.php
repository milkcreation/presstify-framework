<?php

/**
 * @name Radio
 * @desc Champ de selection bouton radio
 * @package presstiFy
 * @namespace tiFy\Components\Field\Radio
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Field\Radio;

use tiFy\Field\AbstractFieldItem;

class Radio extends AbstractFieldItem
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var array $attrs Liste des propriétés de la balise HTML.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var string $value Attribut de configuration de la valeur de soumission du champ "value" si l'élément est selectionné.
     *      @var null|string $checked Valeur de la selection.
     * }
     */
    protected $attributes = [
        'before'  => '',
        'after'   => '',
        'attrs'   => [],
        'name'    => '',
        'value'   => '',
        'checked' => null
    ];

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->setAttr('type', 'radio');

        if ($this->isChecked()) :
            $this->setAttr('checked', 'checked');
        endif;
    }
}