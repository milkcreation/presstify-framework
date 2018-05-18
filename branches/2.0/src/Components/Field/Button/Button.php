<?php

/**
 * @name Button
 * @desc Bouton d'action
 * @package presstiFy
 * @namespace tiFy\Components\Field\Button
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Field\Button;

use tiFy\Field\AbstractFieldController;

class Button extends AbstractFieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var array $attrs Liste des propriétés de la balise HTML.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var string $value Attribut de configuration de la valeur initiale de soumission du champ "value".
     *      @var string $type Type de bouton. button par défaut.
     *      @var string $content Contenu de la balise HTML.
     * }
     */
    protected $attributes = [
        'before'  => '',
        'after'   => '',
        'attrs'   => [],
        'name'    => '',
        'value'   => '',
        'type'    => 'button',
        'content' => ''
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
        $this->attributes['content'] = __('Envoyer', 'tify');

        parent::parse($attrs);

        if (!isset($this->attributes['attrs']['id'])) :
            $this->attributes['attrs']['id'] = 'tiFyField-button--' . $this->getIndex();
        endif;

        if (!isset($this->attributes['attrs']['type'])) :
            $this->attributes['attrs']['type'] = $this->attributes['type'];
        endif;
    }

    /**
     * Affichage.
     *
     * @return string
     */
    protected function display()
    {
        ob_start();
?><?php $this->before(); ?><button <?php $this->attrs(); ?>><?php $this->content(); ?></button><?php $this->after(); ?><?php

        return ob_get_clean();
    }
}