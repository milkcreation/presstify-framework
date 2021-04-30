<?php declare(strict_types=1);

namespace tiFy\Form\Factory;

use ArrayIterator, LogicException;
use Illuminate\Support\Collection;
use tiFy\Contracts\Form\ButtonDriver as ButtonDriverContract;
use tiFy\Contracts\Form\ButtonsFactory as ButtonsFactoryContract;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Form\ButtonDriver;
use tiFy\Form\Concerns\FormAwareTrait;

class ButtonsFactory implements ButtonsFactoryContract
{
    use FormAwareTrait;

    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Liste des pilotes de boutons déclarés.
     * @var ButtonDriverContract[][]|array
     */
    protected $buttonDrivers = [];

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->buttonDrivers;
    }

    /**
     * @inheritDoc
     */
    public function boot(): ButtonsFactoryContract
    {
        if ($this->booted === false) {
            if (!$this->form() instanceof FormFactoryContract) {
                throw new LogicException('Missing valid FormFactory');
            }

            $this->form()->event('buttons.boot', [&$this]);

            $buttons = (array)$this->form()->params('buttons', []);

            if (!isset($buttons['submit'])) {
                $buttons['submit'] = true;
            }

            $_buttons = [];
            foreach ($buttons as $alias => $params) {
                if (is_numeric($alias)) {
                    if (is_string($params)) {
                        $alias = $params;
                        $params = [];
                    } else {
                        continue;
                    }
                }

                if ($params !== false) {
                    if ($driver = $this->form()->formManager()->getButtonDriver($alias)) {
                        $_buttons[$alias] = $driver;
                    } else {
                        $_buttons[$alias] = new ButtonDriver();
                    }

                    $_buttons[$alias]->setForm($this->form())->setParams($params)->boot();
                }
            }

            $max = $this->collect($_buttons)->max(function (ButtonDriverContract $button) {
                return $button->getPosition();
            });

            if ($max) {
                $pad = 0;
                $this->collect($_buttons)->each(function (ButtonDriverContract $button) use (&$pad, $max) {
                    $position = $button->getPosition() ?: ++$pad + $max;

                    return $button->params(['position' => absint($position)]);
                });
            }

            $this->buttonDrivers = $this->collectByPosition()->all();

            $this->booted = true;

            $this->form()->event('buttons.booted', [&$this]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function collect(?array $items = null): iterable
    {
        return new Collection($items ?? $this->buttonDrivers);
    }

    /**
     * @inheritDoc
     */
    public function collectByPosition(): iterable
    {
        return $this->collect()->sortBy(function (ButtonDriverContract $button) {
            return $button->getPosition();
        });
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->buttonDrivers);
    }

    /**
     * @inheritDoc
     */
    public function get(string $alias): ?ButtonDriverContract
    {
        return $this->buttonDrivers[$alias] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->buttonDrivers);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->buttonDrivers);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): ?ButtonDriverContract
    {
        return $this->buttonDrivers[$offset] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->buttonDrivers[] = $value;
        } else {
            $this->buttonDrivers[$offset] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->buttonDrivers[$offset]);
    }
}