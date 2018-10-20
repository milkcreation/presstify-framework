<?php

namespace tiFy\Field\Label;

use tiFy\Field\FieldController;

class Label extends FieldController
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
}