<?php

namespace tiFy\Form\Field;

use tiFy\Contracts\Form\FieldManager;

final class Manager
{
    /**
     * Liste des attributs de support des types de champs déclarés.
     * @var array
     */
    protected $supports = [
        'button'              => ['request', 'wrapper'],
        'checkbox-collection' => ['label', 'request', 'tabindexes', 'wrapper'],
        'datetime-js'         => ['label', 'request', 'tabindexes', 'wrapper'],
        'hidden'              => ['request'],
        'label'               => ['wrapper'],
        'radio-collection'    => ['label', 'request', 'tabindexes', 'wrapper'],
        'repeater'            => ['label', 'request', 'tabindexes', 'wrapper'],
        'submit'              => ['request', 'tabindex', 'wrapper'],
        'toggle-switch'       => ['request', 'tabindex', 'wrapper'],
    ];
}