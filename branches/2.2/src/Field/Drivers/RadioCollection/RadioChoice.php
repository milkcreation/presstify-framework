<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers\RadioCollection;

use tiFy\Field\Drivers\LabelDriverInterface;
use tiFy\Field\Drivers\RadioDriverInterface;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Field;

class RadioChoice extends ParamsBag implements RadioChoiceInterface
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
     * Nom de qualification.
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
     * Instance du bouton radio.
     * @var RadioDriverInterface
     */
    protected $radio;

    /**
     * Instance du gestionnaire d'affichage de la liste des éléments.
     * @var RadioWalkerInterface
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

        if ($attrs instanceof RadioDriverInterface) {
            $this->radio = $attrs;
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
    public function build(): RadioChoiceInterface
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
            'label' => [
                'before'  => '',
                'after'   => '',
                'content' => '',
                'attrs'   => [],
            ],
            'radio' => [
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
        return $this->getRadio() instanceof RadioDriverInterface ? $this->getRadio()->getName() : '';
    }

    /**
     * @inheritDoc
     */
    public function getRadio(): RadioDriverInterface
    {
        return $this->radio;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->getRadio() instanceof RadioDriverInterface ? $this->getRadio()->getValue() : null;
    }

    /**
     * @inheritDoc
     */
    public function isChecked(): bool
    {
        return $this->getRadio() instanceof RadioDriverInterface ? $this->getRadio()->isChecked() : false;
    }

    /**
     * @inheritDoc
     */
    public function parse(): RadioChoiceInterface
    {
        parent::parse();

        if (!$this->get('attrs.id')) {
            $this->set('attrs.id', 'FieldRadioCollection-item--' . $this->index);
        }

        if (!$this->get('radio.attrs.id')) {
            $this->set('radio.attrs.id', 'FieldRadioCollection-itemInput--' . $this->index);
        }

        if (!$this->get('radio.attrs.class')) {
            $this->set('radio.attrs.class', 'FieldRadioCollection-itemInput');
        }

        if (!$this->get('label.attrs.id')) {
            $this->set('label.attrs.id', 'FieldRadioCollection-itemLabel--' . $this->index);
        }

        if (!$this->get('label.attrs.class')) {
            $this->set('label.attrs.class', 'FieldRadioCollection-itemLabel');
        }

        if (!$this->get('label.attrs.for')) {
            $this->set('label.attrs.for', 'FieldRadioCollection-itemInput--' . $this->index);
        }

        $this->radio = Field::get('radio', $this->get('radio', []));
        $this->label = Field::get('label', $this->get('label', []));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->getRadio()->render() . $this->getLabel()->render();
    }

    /**
     * @inheritDoc
     */
    public function setNameAttr(string $name): RadioChoiceInterface
    {
        if ($this->getRadio() instanceof RadioDriverInterface) {
            $this->getRadio()->set('attrs.name', $name);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setChecked(): RadioChoiceInterface
    {
        if ($this->getRadio() instanceof RadioDriverInterface) {
            $this->getRadio()->push('attrs', 'checked');
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setWalker(RadioWalkerInterface $walker): RadioChoiceInterface
    {
        $this->walker = $walker;

        return $this;
    }
}