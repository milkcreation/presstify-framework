<?php
/**
 * Encapsulation de l'intitulé de champ de formulaire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FactoryView $this
 * @var tiFy\Contracts\Form\FactoryField $field
 */
echo partial('tag', array_merge($field->get('label.wrapper', []), [
    'content' => $this->section('content')
]));