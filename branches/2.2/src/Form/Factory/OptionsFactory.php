<?php

declare(strict_types=1);

namespace tiFy\Form\Factory;

use BadMethodCallException;
use Exception;
use LogicException;
use tiFy\Contracts\Form\OptionsFactory as OptionsFactoryContract;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Form\Concerns\FormAwareTrait;
use tiFy\Support\Concerns\ParamsBagTrait;
use Throwable;

/**
 * @mixin \tiFy\Support\ParamsBag
 */
class OptionsFactory implements OptionsFactoryContract
{
    use FormAwareTrait;
    use ParamsBagTrait;

    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Délégation d'appel des méthodes du ParamBag.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function __call(string $method, array $arguments)
    {
        try {
            return $this->params()->{$method}(...$arguments);
        } catch(Exception $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new BadMethodCallException(
                sprintf(
                    '[%s] Delegate ParamsBag method call [%s] throws an exception: %s',
                    __CLASS__,
                    $method,
                    $e->getMessage()
                ), 0, $e
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function boot(): OptionsFactoryContract
    {
        if ($this->booted === false) {
            if (!$this->form() instanceof FormFactoryContract) {
                throw new LogicException('Missing valid FormFactory');
            }

            $this->form()->event('options.boot');

            $this->params($this->form()->params('options', []));
            $this->parseParams();

            $this->form()->event('options.booted');

            $this->booted = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
            /**
             * @var string|bool $anchor Ancre de défilement verticale de la page web à la soumission du formulaire.
             */
            'anchor'         => false,
            /**
             *
             */
            'error'   => [
                'title'       => '',
                'show'        => -1,
                'teaser'      => '...',
                'field'       => false,
                'dismissible' => false,
            ],
            /**
             *
             */
            'success' => [
                'message' => __(
                    'Votre demande a bien été prise en compte et sera traitée dès que possible.',
                    'tify'
                ),
            ],
        ];
    }
}