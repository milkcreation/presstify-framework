<?php declare(strict_types=1);

namespace tiFy\Form;

use BadMethodCallException;
use Exception;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Contracts\Form\FormView as FactoryViewContract;
use tiFy\View\Factory\PlatesFactory;
use Closure;

/**
 * @method string csrf()
 * @method bool isSuccessed()
 * @method string tagName()
 */
class FormView extends PlatesFactory implements FactoryViewContract
{
    /**
     * Liste des méthodes héritées.
     * @var array
     */
    protected $mixins = [
        'csrf',
        'isSuccessed',
        'tagName'
    ];

    /**
     * @inheritDoc
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, $this->mixins)) {
            try {
                $delegate = $this->engine->params('form');

                return $delegate->{$name}(...$arguments);
            } catch (Exception $e) {
                throw new BadMethodCallException(sprintf(
                    __CLASS__ . ' throws an exception during the method call [%s] with message : %s',
                    $name, $e->getMessage()
                ));
            }
        } else {
            return parent::__call($name, $arguments);
        }
    }

    /**
     * @inheritDoc
     */
    public function after(): string
    {
        if ($content = $this->form()->params('after')) {
            if ($content instanceof Closure) {
                return call_user_func($content);
            } elseif (is_string($content)) {
                return $content;
            }
        }
        return '';
    }

    /**
     * @inheritDoc
     */
    public function before(): string
    {
        if ($content = $this->form()->params('before')) {
            if ($content instanceof Closure) {
                return call_user_func($content);
            } elseif (is_string($content)) {
                return $content;
            }
        }
        return '';
    }

    /**
     * @inheritDoc
     */
    public function form(): FormFactory
    {
        return $this->engine->params('form');
    }
}