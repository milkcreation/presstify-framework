<?php

namespace tiFy\Support;

use tiFy\Contracts\Support\LabelsBag as LabelBagContract;

class LabelsBag extends ParamsBag implements LabelBagContract
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name = $name;

        $this->set($attrs)->parse();
    }

    /**
     * @inheritDoc
     */
    public function defaults()
    {
        return [
            'gender'   => false,
            'plural'   => $this->getName(),
            'singular' => $this->getName(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultEditItem()
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
    public function defaultDatasItem()
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
    public function hasGender(): bool
    {
        return !!$this->get('gender');
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
        return $this->get('plural');
    }

    /**
     * @inheritDoc
     */
    public function getSingular(): string
    {
        return $this->get('singular');
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
     * {@inheritDoc}
     *
     * @return static
     */
    public function parse(): self
    {
        parent::parse();

        $this->set('plural', Str::lower($this->get('plural')));
        $this->set('singular', Str::lower($this->get('singular')));

        return $this;
    }
}