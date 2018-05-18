<?php

/**
 * @name Number
 * @desc Champ de selection de valeur numérique.
 * @package presstiFy
 * @namespace tiFy\Components\Field\Number
 * @version 1.1
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Field\Number;

use tiFy\Field\AbstractFieldController;

class Number extends AbstractFieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var array $attrs Liste des propriétés de la balise HTML.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var int $value Attribut de configuration de la valeur initiale de soumission du champ "value".
     * }
     */
    protected $attributes = [
        'before' => '',
        'after'  => '',
        'attrs'  => [],
        'name'    => '',
        'value'   => 0
    ];

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    protected function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!isset($this->attributes['attrs']['id'])) :
            $this->attributes['attrs']['id'] = 'tiFyField-number--' . $this->getIndex();
        endif;
        $this->attributes['attrs']['type'] = 'number';
    }

    /**
     * Affichage.
     *
     * @return string
     */
    protected function display()
    {
        ob_start();
?><?php $this->before(); ?><input <?php $this->attrs(); ?>/><?php $this->after(); ?><?php

        return ob_get_clean();
    }
}