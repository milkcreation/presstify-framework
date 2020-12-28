<?php declare(strict_types=1);

namespace tiFy\Form\FieldDrivers;

use tiFy\Contracts\Form\TagFieldDriver as TagFieldDriverContract;
use tiFy\Form\FieldDriver;
use tiFy\Support\Proxy\Partial;

class TagFieldDriver extends FieldDriver implements TagFieldDriverContract
{
    /**
     * Liste des propriétés de formulaire supportées.
     * @var array
     */
    protected $supports = ['wrapper'];

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $args = array_merge([
            'tag'     => 'div',
            'attrs'   => $this->params('attrs', []),
            'content' => $this->getValue(),
        ], $this->getExtras());

        return Partial::get('tag', $args)->render();
    }
}