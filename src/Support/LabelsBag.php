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
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
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
     * @inheritdoc
     */
    public function hasGender(): bool
    {
        return !!$this->get('gender');
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getPlural(): string
    {
        return $this->get('plural');
    }

    /**
     * @inheritdoc
     */
    public function getSingular(): string
    {
        return $this->get('singular');
    }

    /**
     * @inheritdoc
     */
    public function isFirstVowel(string $string): bool
    {
        $first = strtolower(mb_substr(remove_accents($string), 0, 1));

        return in_array($first, ['a', 'e', 'i', 'o', 'u', 'y']);
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        parent::parse();

        $this->set('plural', Str::lower($this->get('plural')));
        $this->set('singular', Str::lower($this->get('singular')));
    }
}