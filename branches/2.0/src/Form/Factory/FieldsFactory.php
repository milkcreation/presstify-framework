<?php declare(strict_types=1);

namespace tiFy\Form\Factory;

use ArrayIterator, LogicException;
use Illuminate\Support\Collection;
use tiFy\Contracts\Form\FieldDriver as FieldDriverContract;
use tiFy\Contracts\Form\FieldsFactory as FieldsFactoryContract;
use tiFy\Contracts\Form\FormFactory as FormFactoryContract;
use tiFy\Form\Concerns\FormAwareTrait;
use tiFy\Form\FieldDriver;

class FieldsFactory implements FieldsFactoryContract
{
    use FormAwareTrait;

    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Liste des éléments associés au formulaire.
     * @var FieldDriverContract[]
     */
    protected $fieldDrivers = [];

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->fieldDrivers;
    }

    /**
     * @inheritDoc
     */
    public function boot(): FieldsFactoryContract
    {
        if ($this->booted === false) {
            if (!$this->form() instanceof FormFactoryContract) {
                throw new LogicException('Missing valid FormFactory');
            }

            $this->form()->event('fields.boot', [&$this]);

            $fields = (array)$this->form()->params('fields', []);

            foreach ($fields as $slug => $params) {
                if ($slug !== null) {
                    if (!$alias = $params['type'] ?? null) {
                        throw new LogicException('Missing type in FormField declaration');
                    }

                    $this->fieldDrivers[$slug] = $this->form()->formManager()->getFieldDriver($alias)
                        ?: (new FieldDriver())->setAlias($alias);
                    $this->fieldDrivers[$slug]->setSlug($slug)->setForm($this->form())->setParams($params)->boot();

                    if (!$this->fieldDrivers[$slug]->getGroup()) {
                        $this->form()->groups()->setDriver((string)$this->fieldDrivers[$slug]->params('group'), []);
                    }
                }
            }

            $this->booted = true;

            $this->form()->event('fields.booted', [&$this]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function collect(?array $items = null): iterable
    {
        return new Collection($items ?? $this->fieldDrivers);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->fieldDrivers);
    }

    /**
     * @inheritDoc
     */
    public function get(string $alias): ?FieldDriverContract
    {
        return $this->fieldDrivers[$alias] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->fieldDrivers);
    }

    /**
     * @inheritDoc
     */
    public function fromGroup(string $groupAlias): ?iterable
    {
        return $this->collect()->filter(function (FieldDriverContract $field) use ($groupAlias) {
            return $field->getGroup()->getAlias() === $groupAlias;
        });
    }

    /**
     * @inheritDoc
     */
    public function metatagsValue($tags, $raw = true): ?string
    {
        if (is_string($tags)) {
            if (preg_match_all('/([^%%]*)%%(.*?)%%([^%%]*)?/', $tags, $matches)) {
                $tags = '';
                foreach ($matches[2] as $i => $slug) {
                    $tags .= $matches[1][$i] . (($field = $this->get($slug))
                            ? $field->getValue($raw) : $matches[2][$i]) . $matches[3][$i];
                }
            }
        } elseif (is_array($tags)) {
            foreach ($tags as $k => &$i) {
                $i = $this->metatagsValue($i, $raw);
            }
        }

        return $tags;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->fieldDrivers);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset): ?FieldDriverContract
    {
        return $this->fieldDrivers[$offset] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->fieldDrivers[] = $value;
        } else {
            $this->fieldDrivers[$offset] = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->fieldDrivers[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function preRender(): FieldsFactoryContract
    {
        foreach($this->all() as $fieldDriver) {
            $fieldDriver->preRender();
        }

        return $this;
    }
}