<?php declare(strict_types=1);

namespace tiFy\Form\FieldDrivers;

use Closure;
use tiFy\Contracts\Form\HtmlFieldDriver as HtmlFieldDriverContract;
use tiFy\Form\FieldDriver;

class HtmlFieldDriver extends FieldDriver implements HtmlFieldDriverContract
{
    /**
     * Liste des propriétés de formulaire supportées.
     * @var array
     */
    protected $supports = [];

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $value = $this->getValue();

        return !$value instanceof Closure ? (string)$value : $value();
    }
}