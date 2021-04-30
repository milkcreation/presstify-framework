<?php

declare(strict_types=1);

namespace tiFy\Field;

use Exception;
use BadMethodCallException;
use tiFy\View\Factory\PlatesFactory;

/**
 * @method string after()
 * @method string attrs()
 * @method string before()
 * @method string content()
 * @method string getAlias()
 * @method string getId()
 * @method string getIndex()
 * @method string getName()
 * @method string getValue()
 */
class FieldView extends PlatesFactory implements FieldViewInterface
{
    /**
     * Liste des méthodes héritées.
     * @var array
     */
    protected $mixins = [
        'after',
        'attrs',
        'before',
        'content',
        'getAlias',
        'getId',
        'getIndex',
        'getName',
        'getValue',
    ];

    /**
     * @inheritDoc
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, $this->mixins)) {
            try {
                $delegate = $this->engine->params('driver');

                return $delegate->{$name}(...$arguments);
            } catch (Exception $e) {
                throw new BadMethodCallException(
                    sprintf(
                        __CLASS__ . ' throws an exception during the method call [%s] with message : %s',
                        $name,
                        $e->getMessage()
                    )
                );
            }
        } else {
            return parent::__call($name, $arguments);
        }
    }
}