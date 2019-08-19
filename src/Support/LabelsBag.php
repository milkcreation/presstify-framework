<?php declare(strict_types=1);

namespace tiFy\Support;

use tiFy\Contracts\Support\{LabelsBag as LabelsBagContract, ParamsBag as ParamsBagContract};

class LabelsBag extends ParamsBag implements LabelsBagContract
{
    /**
     * Indicateur de gestion du féminin.
     * @var boolean
     */
    protected $gender = false;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Intitulé de qualification du pluriel d'un élément.
     * @var string
     */
    protected $plural = '';

    /**
     * Intitulé de qualification du singulier d'un élément.
     * @var string
     */
    protected $singular = '';

    /**
     * @inheritDoc
     */
    public static function createFromAttrs($attrs, ?string $name = null): ParamsBagContract
    {
        $self = new static();
        if (!is_null($name)) {
            $self->setName($name);
        }

        return $self->set($attrs)->parse();
    }

    /**
     * @inheritDoc
     */
    public function defaultEditItem(): string
    {
        return sprintf(
            __('Éditer %s %s', 'tify'),
            $this->getDeterminant($this->getSingular()),
            $this->getSingular()
        );
    }

    /**
     * @inheritDoc
     */
    public function defaultDatasItem(): string
    {
        if (self::isFirstVowel($this->getSingular())) {
            $determinant = __('de l\'', 'tify');
        } elseif ($this->hasGender()) {
            $determinant = __('de la', 'tify');
        } else {
            $determinant = __('du', 'tify');
        }
        return sprintf(__('Données %s %s', 'tify'), $determinant, $this->getSingular());
    }

    /**
     * @inheritDoc
     */
    public function getDeterminant(string $string): string
    {
        if (self::isFirstVowel($string)) {
            return __("l'", 'tify');
        } else {
            return $this->hasGender() ? __("la", 'tify') : __("le", 'tify');
        }
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getPlural(): string
    {
        return $this->plural ? : __('éléments', 'tify');
    }

    /**
     * @inheritDoc
     */
    public function getSingular(): string
    {
        return $this->singular ? : __('élément', 'tify');
    }

    /**
     * @inheritDoc
     */
    public function hasGender(): bool
    {
        return $this->gender;
    }

    /**
     * @inheritDoc
     */
    public function isFirstVowel(string $string): bool
    {
        $first = strtolower(mb_substr(remove_accents($string), 0, 1));

        return in_array($first, ['a', 'e', 'i', 'o', 'u', 'y']);
    }

    /**
     * @inheritDoc
     */
    public function parse(): LabelsBagContract
    {
        if ($this->has('gender')) {
            $this->setGender(!!$this->pull('gender'));
        }

        if ($this->has('plural')) {
            $this->setPlural(Str::lower($this->pull('plural')));
        }

        if ($this->has('singular')) {
            $this->setSingular(Str::lower($this->pull('singular')));
        }

        parent::parse();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setGender(bool $gender): LabelsBagContract
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): LabelsBagContract
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPlural(string $plural): LabelsBagContract
    {
        $this->plural = $plural;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSingular(string $singular): LabelsBagContract
    {
        $this->singular = $singular;

        return $this;
    }
}