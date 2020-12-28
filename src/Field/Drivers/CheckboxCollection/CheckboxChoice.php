<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers\CheckboxCollection;

use tiFy\Field\Drivers\CheckboxDriverInterface;
use tiFy\Field\Drivers\LabelDriverInterface;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Field;

class CheckboxChoice extends ParamsBag implements CheckboxChoiceInterface
{
    /**
     * Compteur d'indice.
     * @var int
     */
    private static $_index = 0;

    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    private $built = false;

    /**
     * Instance de la case à cocher.
     * @var CheckboxDriverInterface
     */
    protected $checkbox;

    /**
     * Identifiant de qualification.
     * @var string|int
     */
    protected $id = '';

    /**
     * Indice de qualification.
     * @var int
     */
    protected $index = 0;

    /**
     * Instance de l'intitulé.
     * @var LabelDriverInterface
     */
    protected $label;

    /**
     * Instance du gestionnaire d'affichage de la liste des éléments.
     * @var CheckboxWalkerInterface
     */
    protected $walker;

    /**
     * CONSTRUCTEUR.
     *
     * @param string|int $id Identifiant de qualification.
     * @param array|string $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($id, $attrs)
    {
        $this->id = $id;
        $this->index = self::$_index++;

        if (is_string($attrs)) {
            $attrs = [
                'label' => [
                    'content' => $attrs,
                ],
            ];
        }

        if ($attrs instanceof CheckboxDriverInterface) {
            $this->checkbox = $attrs;
        } else {
            $this->set($attrs);
        }
    }

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
    public function build(): CheckboxChoiceInterface
    {
        if (!$this->built) {
            $this->parse();

            $this->built = true;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'label'    => [
                'before'  => '',
                'after'   => '',
                'content' => '',
                'attrs'   => [],
            ],
            'checkbox' => [
                'before'  => '',
                'after'   => '',
                'attrs'   => [],
                'name'    => '',
                'value'   => '',
                'checked' => $this->id,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getCheckbox(): CheckboxDriverInterface
    {
        return $this->checkbox;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): LabelDriverInterface
    {
        return $this->label;
    }

    /**
     * @inheritDoc
     */
    public function getNameAttr(): string
    {
        return $this->getCheckbox() instanceof CheckboxDriverInterface ? $this->getCheckbox()->getName() : '';
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->getCheckbox() instanceof CheckboxDriverInterface ? $this->getCheckbox()->getValue() : null;
    }

    /**
     * @inheritDoc
     */
    public function isChecked(): bool
    {
        return $this->getCheckbox() instanceof CheckboxDriverInterface ? $this->getCheckbox()->isChecked() : false;
    }

    /**
     * @inheritDoc
     */
    public function parse(): CheckboxChoiceInterface
    {
        parent::parse();

        if (!$this->get('attrs.id')) {
            $this->set('attrs.id', 'FieldCheckboxCollection-item--' . $this->index);
        }

        if (!$this->get('checkbox.attrs.id')) {
            $this->set('checkbox.attrs.id', 'FieldCheckboxCollection-itemInput--' . $this->index);
        }

        if (!$this->get('checkbox.attrs.class')) {
            $this->set('checkbox.attrs.class', 'FieldCheckboxCollection-itemInput');
        }

        if (!$this->get('label.attrs.id')) {
            $this->set('label.attrs.id', 'FieldCheckboxCollection-itemLabel--' . $this->index);
        }

        if (!$this->get('label.attrs.class')) {
            $this->set('label.attrs.class', 'FieldCheckboxCollection-itemLabel');
        }

        if (!$this->get('label.attrs.for')) {
            $this->set('label.attrs.for', 'FieldCheckboxCollection-itemInput--' . $this->index);
        }

        $this->checkbox = Field::get('checkbox', $this->get('checkbox', []));
        $this->label = Field::get('label', $this->get('label', []));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->getCheckbox()->render() . $this->getLabel()->render();
    }

    /**
     * @inheritDoc
     */
    public function setNameAttr(string $name): CheckboxChoiceInterface
    {
        if ($this->getCheckbox() instanceof CheckboxDriverInterface) {
            $this->getCheckbox()->set('attrs.name', $name);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setChecked(): CheckboxChoiceInterface
    {
        if ($this->getCheckbox() instanceof CheckboxDriverInterface) {
            $this->getCheckbox()->push('attrs', 'checked');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setWalker(CheckboxWalkerInterface $walker): CheckboxChoiceInterface
    {
        $this->walker = $walker;

        return $this;
    }
}