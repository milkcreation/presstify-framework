<?php

namespace tiFy\Field\Fields\Text;

use tiFy\Contracts\Field\Text as TextContract;
use tiFy\Field\FieldController;

class Text extends FieldController implements TextContract
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Clé d'indice de la valeur de soumission du champ.
     *      @var string $value Valeur courante de soumission du champ.
     *      @var array $attrs Attributs HTML du champ.
     *      @var array $viewer Liste des attributs de configuration du controleur de gabarit d'affichage.
     * }
     */
    protected $attributes = [
        'before' => '',
        'after'  => '',
        'name'   => '',
        'value'  => '',
        'attrs'  => [],
        'viewer' => [],
    ];

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->get('attrs.type')) :
            $this->set('attrs.type', 'text');
        endif;
    }
}