<?php declare(strict_types=1);

namespace tiFy\Form\Factory;

use ArrayIterator, LogicException;
use Illuminate\Support\Collection;
use tiFy\Contracts\Form\AddonDriver as AddonDriverContract;
use tiFy\Contracts\Form\AddonsFactory as AddonsFactoryContract;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Form\AddonDriver;
use tiFy\Form\Concerns\FormAwareTrait;

class AddonsFactory implements AddonsFactoryContract
{
    use FormAwareTrait;

    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Liste pilotes d'addons déclarés.
     * @var AddonDriverContract[]|array
     */
    protected $addonDrivers = [];

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->addonDrivers;
    }

    /**
     * @inheritDoc
     */
    public function boot(): AddonsFactoryContract
    {
        if ($this->booted === false) {
            if (!$this->form() instanceof FormFactoryContract) {
                throw new LogicException('Missing valid FormFactory');
            }

            $this->form()->event('addons.booted', [&$this]);

            $addons = (array)$this->form()->params('addons', []);

            foreach ($addons as $alias => $params) {
                if (is_numeric($alias)) {
                    if (is_string($params)) {
                        $alias = $params;
                        $params = [];
                    } else {
                        continue;
                    }
                }

                if ($params !== false) {
                    if ($driver = $this->form()->formManager()->getAddonDriver($alias)) {
                        $this->addonDrivers[$alias] = $driver;
                    } else {
                        $this->addonDrivers[$alias] = $this->form()->formManager()->registerAddonDriver($alias);
                    }

                    $this->addonDrivers[$alias]->setForm($this->form())->setParams($params)->boot();
                }
            }

            $this->booted = true;

            $this->form()->event('addons.booted', [&$this]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function collect(?array $items = null): iterable
    {
        return new Collection($items ?? $this->addonDrivers);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->addonDrivers);
    }

    /**
     * @inheritDoc
     */
    public function get(string $alias): ?AddonDriverContract
    {
        return $this->addonDrivers[$alias] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->addonDrivers);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->addonDrivers);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): ?AddonDriverContract
    {
        return $this->fieldDrivers[$offset] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->addonDrivers[] = $value;
        } else {
            $this->addonDrivers[$offset] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->addonDrivers[$offset]);
    }
}