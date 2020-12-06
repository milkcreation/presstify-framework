<?php declare(strict_types=1);

namespace tiFy\Form;

use LogicException;
use tiFy\Contracts\Form\ButtonDriver as ButtonDriverContract;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\Concerns\FormAwareTrait;
use tiFy\Support\Concerns\ParamsBagTrait;
use tiFy\Support\Proxy\Field;
use tiFy\Support\ParamsBag;

class ButtonDriver implements ButtonDriverContract
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
     * Instance du formulaire associé.
     * @var FormFactory|null
     */
    protected $form;

    /**
     * Instance du gestionnaire de paramètres
     * @var ParamsBag|null
     */
    protected $paramsBag;

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function boot(): ButtonDriverContract
    {
        if (!$this->isBooted()) {
            if (!$this->form() instanceof FormFactory) {
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
    public function build(): ButtonDriverContract
    {
        if (!$this->isBuilt()) {
            if ($this->alias === null) {
                throw new LogicException('Missing valid alias');
            }

            $this->built = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
            'after'           => '',
            'attrs'           => [],
            'before'          => '',
            'label'           => '',
            'position'        => 0,
            'type'            => '',
            'wrapper'         => true
        ];
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
    public function getPosition(): int
    {
        return (int)$this->params('position', 0);
    }

    /**
     * @inheritDoc
     */
    public function hasWrapper(): bool
    {
        return !empty($this->params('wrapper'));
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
    public function setAlias(string $alias): ButtonDriverContract
    {
        if ($this->alias === null) {
            $this->alias = $alias;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if ($wrapper = $this->params('wrapper')) {
            $wrapper = (is_array($wrapper)) ? $wrapper : [];
            $this->params(['wrapper' => array_merge(['tag' => 'div', 'attrs' => []], $wrapper)]);

            if (!$this->params()->has('wrapper.attrs.id')) {
                $this->params(['wrapper.attrs.id' => "FormButton--{$this->getAlias()}_{$this->form()->getIndex()}"]);
            }

            if (!$this->params('wrapper.attrs.id')) {
                $this->params()->pull('wrapper.attrs.id');
            }

            $default_class = "FormButton FormButton--{$this->getAlias()}";
            if (!$this->params()->has('wrapper.attrs.class')) {
                $this->params(['wrapper.attrs.class' => $default_class]);
            } else {
                $this->params([
                    'wrapper.attrs.class' => sprintf($this->params('wrapper.attrs.class', ''), $default_class)
                ]);
            }

            if (!$this->params('wrapper.attrs.class')) {
                $this->params()->pull('wrapper.attrs.class');
            }
        }

        return Field::get('button', $this->params()->all())->render();
    }
}