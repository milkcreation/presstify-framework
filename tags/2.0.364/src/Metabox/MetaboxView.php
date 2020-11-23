<?php declare(strict_types=1);

namespace tiFy\Metabox;

use BadMethodCallException;
use Exception;
use tiFy\Contracts\Metabox\MetaboxView as MetaboxViewContract;
use tiFy\View\Factory\PlatesFactory;

/**
 * @method string name()
 * @method mixed params(string|array|null $key = null, mixed $default = null)
 * @method mixed value(string|null $key = null, mixed $default = null)
 */
class MetaboxView extends PlatesFactory implements MetaboxViewContract
{
    /**
     * Liste des méthodes de délégation permises.
     * @var array
     */
    protected $mixins = [
        'name',
        'params',
        'value',
    ];

    /**
     * @inheritDoc
     */
    public function __call($name, $args)
    {
        if (in_array($name, $this->mixins)) {
            try {
                $driver = $this->engine->params('driver');

                return $driver->{$name}(...$args);
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