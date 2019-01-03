<?php

namespace tiFy\Field\Fields\Hidden;

use tiFy\Contracts\Field\Hidden as HiddenContract;
use tiFy\Field\FieldController;

class Hidden extends FieldController implements HiddenContract
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Clé d'indice de la valeur de soumission du champ.
     *      @var string $value Valeur courante de soumission du champ.
     *      @var array $attrs Attributs HTML du champ.
     *      @var array $viewer Liste des attributs de configuration du controleur de gabarit d'affichage.
     * }
     */
    protected $attributes = [
        'before'        => '',
        'after'         => '',
        'name'          => '',
        'value'         => '',
        'attrs'         => [],
        'viewer'        => []
    ];

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('attrs.type', 'hidden');
    }
}