<?php declare(strict_types=1);

namespace tiFy\Form\Factory;

use ArrayIterator, LogicException;
use Illuminate\Support\Collection;
use tiFy\Contracts\Form\FieldDriver as FieldDriverContract;
use tiFy\Contracts\Form\FieldGroupDriver as FieldGroupDriverContract;
use tiFy\Contracts\Form\FieldGroupsFactory as FieldGroupsFactoryContract;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Form\Concerns\FormAwareTrait;
use tiFy\Form\FieldGroupDriver;

class FieldGroupsFactory implements FieldGroupsFactoryContract
{
    use FormAwareTrait;

    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Valeur incrémentale de l'indice de qualification.
     * @var int
     */
    protected $increment = 0;

    /**
     * Liste des groupe déclarés.
     * @var FieldGroupDriverContract[]
     */
    protected $groupDrivers = [];

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->groupDrivers;
    }

    /**
     * @inheritDoc
     */
    public function boot(): FieldGroupsFactoryContract
    {
        if ($this->booted === false) {
            if (!$this->form() instanceof FormFactoryContract) {
                throw new LogicException('Missing valid FormFactory');
            }

            $this->form()->event('groups.boot');

            $max = $this->collect()->max(function (FieldGroupDriverContract $group) {
                return $group->getPosition();
            });

            $pad = 0;
            $this->collect()->each(function (FieldGroupDriverContract $group) use (&$pad, $max) {
                $group->boot();

                $group->params('position', (int)$group->getPosition() ?: ++$pad + $max);

                if ($fields = $group->getFields()) {
                    $max = $fields->max(function (FieldDriverContract $field) {
                        return $field->getPosition();
                    });
                    $pad = 0;

                    $fields->each(function (FieldDriverContract $field) use (&$pad, $max) {
                        $number = 10000 * ($field->getGroup()->getPosition() + 1);
                        $position = $field->getPosition() ?: ++$pad + $max;

                        return $field->setPosition(absint($number + $position));
                    });
                }
            });

            $this->booted = true;

            $this->form()->event('groups.booted');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function collect(?array $items = null): iterable
    {
        return new Collection($items ?? $this->groupDrivers);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->groupDrivers);
    }

    /**
     * @inheritDoc
     */
    public function get(string $alias): ?FieldGroupDriverContract
    {
        return $this->groupDrivers[$alias] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function getIncrement(): int
    {
        return $this->increment++;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->groupDrivers);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $$this->groupDrivers);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): ?FieldGroupDriverContract
    {
        return $this->fieldDrivers[$offset] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->groupDrivers[] = $value;
        } else {
            $this->groupDrivers[$offset] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->groupDrivers[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function setDriver(string $alias, $driverDefinition = []): FieldGroupsFactoryContract
    {
        if (!$driverDefinition instanceof FieldGroupDriverContract) {
            $driver = new FieldGroupDriver();
        } else {
            $driver = $driverDefinition;
        }

        $this->groupDrivers[$alias] = $driver->setAlias($alias)->setGroupManager($this);

        return $this;
    }
}