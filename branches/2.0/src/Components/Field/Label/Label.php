<?php

/**
 * @name Label
 * @desc Libelé de champ
 * @package presstiFy
 * @namespace tiFy\Components\Field\Label
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Field\Label;

use tiFy\Field\AbstractFieldController;

class Label extends AbstractFieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $content Contenu de la balise champ.
     *      @var array $attrs Liste des propriétés de la balise HTML.
     * }
     */
    protected $attributes = [
        'before'       => '',
        'after'        => '',
        'content'      => '',
        'attrs'        => []
    ];

    /**
     * Affichage.
     *
     * @return string
     */
    protected function display()
    {
        ob_start();
?><?php $this->before(); ?><label <?php $this->attrs(); ?>/><?php $this->content(); ?></label><?php $this->after(); ?><?php

        return ob_get_clean();
    }
}