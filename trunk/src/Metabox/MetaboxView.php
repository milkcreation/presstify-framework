<?php

declare(strict_types=1);

namespace tiFy\Metabox;

use BadMethodCallException;
use Exception;
use tiFy\View\Factory\PlatesFactory;

/**
 * @method string getName()
 * @method mixed getValue(string|null $key = null, mixed $default = null)
 */
class MetaboxView extends PlatesFactory implements MetaboxViewInterface
{
    /**
     * Liste des méthodes de délégation permises.
     * @var array
     */
    protected $mixins = [
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
                $driver = $this->engine->params('driver');
                return $driver->{$name}(...$arguments);
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