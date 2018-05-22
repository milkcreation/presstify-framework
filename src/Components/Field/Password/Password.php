<?php

/**
 * @name Password
 * @desc Champ de saisie de mot de passe
 * @package presstiFy
 * @namespace tiFy\Components\Field\Password
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Field\Password;

use tiFy\Field\AbstractFieldController;

/**
 * @param array $args {
 *      Liste des attributs de configuration du champ
 *

 * }
 */
class Password extends AbstractFieldController
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
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    protected function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->setAttr('type', 'password');
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