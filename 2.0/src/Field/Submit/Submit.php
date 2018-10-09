<?php

/**
 * @name Submit
 * @desc Champ de soumission de formulaire
 * @package presstiFy
 * @namespace tiFy\Field\Submit
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Field\Submit;

use tiFy\Field\AbstractFieldItem;

class Submit extends AbstractFieldItem
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
    public function defaults()
    {
        return [
            'value' => __('Envoyer', 'tify')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('attrs.type', 'submit');
    }
}