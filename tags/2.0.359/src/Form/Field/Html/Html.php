<?php declare(strict_types=1);

namespace tiFy\Form\Field\Html;

use Closure;
use tiFy\Form\FieldController;

class Html extends FieldController
{
    /**
     * Liste des propriétés de formulaire supportées.
     * @var array
     */
    protected $supports = [];

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $value = $this->field()->getValue();

        return !$value instanceof Closure ? (string)$value : call_user_func($value);
    }
}