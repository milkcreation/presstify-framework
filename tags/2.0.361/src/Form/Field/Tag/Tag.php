<?php declare(strict_types=1);

namespace tiFy\Form\Field\Tag;

use tiFy\Form\FieldController;

class Tag extends FieldController
{
    /**
     * Liste des propriétés de formulaire supportées.
     * @var array
     */
    protected $supports = ['wrapper'];

    /**
     * @inheritDoc
     */
    public function render()
    {
        $args = array_merge([
            'tag'     => 'div',
            'attrs'   => $this->field()->get('attrs', []),
            'content' => $this->field()->getValue(),
        ], $this->field()->getExtras());

        return partial('tag', $args);
    }
}