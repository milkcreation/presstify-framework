<?php declare(strict_types=1);

namespace tiFy\Form\Factory;

use LogicException;
use tiFy\Contracts\Form\OptionsFactory as OptionsFactoryContract;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Form\Concerns\FormAwareTrait;
use tiFy\Support\Concerns\ParamsBagTrait;

class OptionsFactory implements OptionsFactoryContract
{
    use FormAwareTrait, ParamsBagTrait;

    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

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