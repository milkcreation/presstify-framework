<?php

declare(strict_types=1);

namespace tiFy\Form;

use LogicException;
use tiFy\Contracts\Form\FieldGroupDriver as FieldGroupDriverContract;
use tiFy\Contracts\Form\FieldGroupsFactory as FieldGroupsFactoryContract;
use tiFy\Form\Concerns\FormAwareTrait;
use tiFy\Support\HtmlAttrs;
use tiFy\Support\Concerns\ParamsBagTrait;

class FieldGroupDriver implements FieldGroupDriverContract
{
    use FormAwareTrait, ParamsBagTrait;

    /**
     * Indicateur de chargement.
     * @var bool
     */
    private $booted = false;

    /**
     * Identifiant d'indexation.
     * @var int
     */
    private $index = 0;

    /**
     * Instance du gestionnaire des groupes de champ.
     * @var FieldGroupsFactoryContract
     */
    protected $groupsManager;

    /**
     * Alias de qualification.
     * @var string
     */
    protected $alias = '';

    /**
     * Instance du groupe parent.
     * @var FieldGroupDriverContract|null
     */
    protected $parent;

    /**
     * @inheritDoc
     */
    public function boot(): FieldGroupDriverContract
    {
        if ($this->booted === false) {
            if (!$this->groupsManager() instanceof FieldGroupsFactoryContract) {
                throw new LogicException('Missing valid GroupManager');
            }

            $this->setForm($this->groupsManager->form());

            $this->form()->event('group.boot');

            $this->parseParams();

            $this->booted = true;

            $this->form()->event('group.booted');
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function after(): string
    {
        return $this->params('after');
    }

    /**
     * @inheritdoc
     */
    public function before(): string
    {
        return $this->params('before');
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
            'after'    => '',
            'before'   => '',
            'attrs'    => [],
            'parent'   => null,
            'position' => null
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
    public function getAttrs(bool $linearized = true)
    {
        $attrs = $this->params('attrs', []);

        return $linearized ? HtmlAttrs::createFromAttrs($this->params('attrs', [])) : $attrs;
    }

    /**
     * @inheritDoc
     */
    public function getFields(): iterable
    {
        return $this->form()->fields()->fromGroup($this->getAlias()) ?: [];
    }

    /**
     * @inheritDoc
     */
    public function getPosition(): int
    {
        return (int)$this->params('position');
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?FieldGroupDriverContract
    {
        if (is_null($this->parent)) {
            if ($alias = $this->params('parent')) {
                $this->parent = $this->groupsManager()->get($alias) ?: false;
            } else {
                $this->parent = false;
            }
        }

        return $this->parent ?: null;
    }

    /**
     * @inheritDoc
     */
    public function groupsManager(): ?FieldGroupsFactoryContract
    {
        return $this->groupsManager;
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): FieldGroupDriverContract
    {
        $param = $this->params();

        $class = 'FormFieldsGroup FormFieldsGroup--' . $this->getAlias();

        if (!$param->has('attrs.class')) {
            $param->set('attrs.class', $class);
        } else {
            $param->set('attrs.class', sprintf($param->get('attrs.class'), $class));
        }

        $position = $this->getPosition();
        if (is_null($position)) {
            $position = $this->index;
        }

        $this->params('position', intval($position));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): FieldGroupDriverContract
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setGroupManager(FieldGroupsFactoryContract $groupsManager): FieldGroupDriverContract
    {
        $this->groupsManager = $groupsManager;

        return $this;
    }
}