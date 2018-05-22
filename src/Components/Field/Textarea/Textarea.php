<?php

/**
 * @name Textarea
 * @desc Zone de texte de saisie libre
 * @package presstiFy
 * @namespace tiFy\Components\Field\Textarea
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Field\Textarea;

use tiFy\Field\AbstractFieldController;

class Textarea extends AbstractFieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var array $attrs Liste des propriétés de la balise HTML.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var string $value Attribut de configuration de la valeur initiale de soumission du champ "value".
     * }
     */
    protected $attributes = [
        'before' => '',
        'after'  => '',
        'attrs'  => [],
        'name'   => '',
        'value'  => ''
    ];

    /**
     * Traitement de l'attribut de configuration de la valeur de soumission du champ "value".
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    protected function parseValue($attrs = [])
    {
        if (isset($this->attributes['value'])) :
            $this->attributes['content'] = $this->attributes['value'];
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
?><?php $this->before(); ?><textarea <?php $this->attrs(); ?>><?php $this->content(); ?></textarea><?php $this->after(); ?><?php

        return ob_get_clean();
    }
}