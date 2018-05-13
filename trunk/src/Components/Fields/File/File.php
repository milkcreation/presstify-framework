<?php

/**
 * @name File
 * @desc Champ de téléversement de fichier
 * @package presstiFy
 * @namespace tiFy\Components\Fields\File
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Fields\File;

use tiFy\Field\AbstractFieldController;

class File extends AbstractFieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ
     *      @var string $after Contenu placé après le champ
     *      @var array $attrs Liste des propriétés de la balise HTML
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name"
     *      @var string $value Attribut de configuration de la valeur initiale de soumission du champ "value"
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
     * Traitement des attributs de configuration
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    protected function parse($args = [])
    {
        parent::parse($args);

        if (!isset($this->attributes['attrs']['id'])) :
            $this->attributes['attrs']['id'] = 'tiFyField-file--' . $this->getIndex();
        endif;
        $this->attributes['attrs']['type'] = 'file';
    }

    /**
     * Affichage
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