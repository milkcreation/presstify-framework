<?php declare(strict_types=1);

namespace tiFy\Field;

use Exception;
use BadMethodCallException;
use tiFy\Contracts\Field\FieldView as FieldViewContract;
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
class FieldView extends PlatesFactory implements FieldViewContract
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
        'getValue'
    ];

    /**
     * @inheritDoc
     */
    public function __call($name, $args)
    {
        if (in_array($name, $this->mixins)) {
            try {
                $field = $this->engine->params('field');

                return $field->{$name}(...$args);
            } catch (Exception $e) {
                throw new BadMethodCallException(sprintf(
                    __CLASS__ . ' throws an exception during the method call [%s] with message : %s',
                    $name, $e->getMessage()
                ));
            }
        } else {
            return parent::__call($name, $args);
        }
    }
}