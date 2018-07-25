<?php

/**
 * @name Submit
 * @desc Champ de soumission de formulaire
 * @package presstiFy
 * @namespace tiFy\Components\Field\Submit
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Components\Field\Submit;

use tiFy\Field\AbstractFieldItemController;

class Submit extends AbstractFieldItemController
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
        'name'    => '',
        'value'   => ''
    ];

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->set('value', __('Envoyer', 'tify'));

        parent::parse($attrs);

        $this->setAttr('type', 'submit');
    }
}