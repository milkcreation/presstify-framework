<?php

/**
 * @name Radio
 * @desc Champ de selection bouton radio
 * @package presstiFy
 * @namespace tiFy\Components\Fields\Radio
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Fields\Radio;

use tiFy\Core\Field\AbstractFactory;

/**
 * @param array $args {
 *      Liste des attributs de configuration du champ
 *
 *      @var string $before Contenu placé avant le champ
 *      @var string $after Contenu placé après le champ
 *      @var array $attrs Liste des propriétés de la balise HTML
 *      @var string $name Attribut de configuration de la qualification de soumission du champ "name"
 *      @var string $value Attribut de configuration de la valeur de soumission du champ "value" si l'élément est selectionné
 *      @var null|string $checked Valeur de la selection
 * }
 */
class Radio extends AbstractFactory
{
    /**
     * Traitement des attributs de configuration
     *
     * @return array
     */
    final protected function parse($args = [])
    {
        // Pré-traitement des attributs de configuration
        $args = parent::parse($args);

        // Traitement des attributs de configuration
        $defaults = [
            'before'  => '',
            'after'   => '',
            'attrs'   => [],
            'name'    => '',
            'value'   => '',
            'checked' => null,
        ];
        $args = array_merge($defaults, $args);

        if (!isset($args['attrs']['id'])) :
            $args['attrs']['id'] = 'tiFyField-radio--' . $this->getIndex();
        endif;
        $args['attrs']['type'] = 'radio';

        return $args;
    }

    /**
     * Affichage
     *
     * @return string
     */
    protected function display()
    {
        if ($this->isChecked()) :
            $this->setAttr('checked', 'checked');
        endif;

        ob_start();
?><?php $this->before(); ?><input <?php $this->attrs(); ?>/><?php $this->after(); ?><?php

        return ob_get_clean();
    }
}