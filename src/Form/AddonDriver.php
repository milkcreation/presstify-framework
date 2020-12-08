<?php declare(strict_types=1);

namespace tiFy\Form;

use LogicException;
use tiFy\Contracts\Form\AddonDriver as AddonDriverContract;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Form\Concerns\FormAwareTrait;
use tiFy\Support\Concerns\ParamsBagTrait;

class AddonDriver implements AddonDriverContract
{
    use FormAwareTrait, ParamsBagTrait;

    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    private $built = false;

    /**
     * Alias de qualification.
     * @var string
     */
    protected $alias;

    /**
     * @inheritDoc
     */
    public function boot(): AddonDriverContract
    {
        if (!$this->isBooted()) {
            if (!$this->form() instanceof FormFactoryContract) {
                throw new LogicException('Invalid related FormFactory');
            }

            $this->parseParams();

            $this->booted = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function build(): AddonDriverContract
    {
        if (!$this->isBuilt()) {
            if ($this->alias === null) {
                throw new LogicException('Missing alias');
            }

            $this->built = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaultFormOptions(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function defaultFieldOptions(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @inheritDoc
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }

    /**
     * @inheritDoc
     */
    public function isBuilt(): bool
    {
        return $this->built;
    }

    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): AddonDriverContract
    {
        $this->alias = $alias;

        return $this;
    }
}