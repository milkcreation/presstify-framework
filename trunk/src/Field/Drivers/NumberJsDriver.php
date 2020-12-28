<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Field\FieldDriver;
use tiFy\Field\FieldDriverInterface;
use tiFy\Support\Str;

class NumberJsDriver extends FieldDriver implements NumberJsDriverInterface
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                /**
                 * @var int $value
                 */
                'value'     => 0,
                /**
                 * @var string $container Liste des attribut de configuration du conteneur de champ
                 */
                'container' => [],
                /**
                 * @var array $options Liste des options du contrôleur ajax.
                 * @see http://api.jqueryui.com/spinner/
                 */
                'options'   => [],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function parseAttrClass(): FieldDriverInterface
    {
        $base = 'Field' . ucfirst(preg_replace('/\./', '-', Str::camel($this->getAlias()))) . '-input';

        $default_class = "{$base} {$base}--" . $this->getIndex();
        if (!$this->has('attrs.class')) {
            $this->set('attrs.class', $default_class);
        } else {
            $this->set('attrs.class', sprintf($this->get('attrs.class'), $default_class));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->set('container.attrs.id', 'FieldNumberJs--' . $this->getIndex());

        if ($container_class = $this->get('container.attrs.class')) {
            $this->set('container.attrs.class', "FieldNumberJs {$container_class}");
        } else {
            $this->set('container.attrs.class', 'FieldNumberJs');
        }
        $this->set('container.attrs.data-control', 'number-js');
        $this->set('container.attrs.data-options.spinner', $this->get('options', []));

        if (!$this->has('attrs.id')) {
            $this->set('attrs.id', 'FieldNumberJs-input--' . $this->getIndex());
        }
        $this->set('attrs.type', 'text');

        $this->set('attrs.data-control', 'number-js.input');

        return parent::render();
    }
}