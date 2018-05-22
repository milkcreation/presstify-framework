<?php

namespace tiFy\Components\Labels;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use tiFy\Apps\AppController;

class LabelsBaseController extends AppController implements LabelsControllerInterface
{
    /**
     * Nom de qualification par defaut.
     * @var string
     */
    protected $name = '';

    /**
     * Liste des attributs.
     * @var array
     */
    protected $attributes = [];

    /**
     * Forme plurielle de l'intitulé de l'élément.
     * @var string
     */
    protected $plural = '';

    /**
     * Forme singulière de l'intitulé de l'élément.
     * @var string
     */
    protected $singular = '';

    /**
     * Indicateur de genre féminin.
     * @var bool
     */
    protected $gender = false;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification par défaut
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name = $name;

        $this->parse($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function defaultEditItem()
    {
        return sprintf(
            __('Éditer %s %s', 'tify'),
            $this->getDeterminant($this->singular, $this->gender),
            $this->singular
        );
    }

    /**
     * @return string
     */
    public function defaultDatasItem()
    {
        if (self::isFirstVowel($this->singular)) :
            $determinant = __('de l\'', 'tify');
        elseif ($this->gender) :
            $determinant = __('de la', 'tify');
        else :
            $determinant = __('du', 'tify');
        endif;

        return sprintf(__('Données %s %s', 'tify'), $determinant, $this->singular);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = '')
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getGender()
    {
        return $this->get('gender', $this->gender);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlural()
    {
        return Str::lower($this->get('plural', $this->plural ? : $this->getName()));
    }

    /**
     * {@inheritdoc}
     */
    public function getSingular()
    {
        return Str::lower($this->get('singular', $this->singular ? : $this->getName()));
    }

    /**
     * Traitement des intitulés.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return array
     */
    protected function parse($attrs = [])
    {
        $this->attributes = $attrs;

        $this->set('gender', $this->getGender());

        $this->set('plural', $this->getPlural());

        $this->set('singular', $this->getSingular());
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isFirstVowel($string)
    {
        $first = strtolower(mb_substr(\remove_accents($string), 0, 1));

        return in_array($first, ['a', 'e', 'i', 'o', 'u', 'y']);
    }

    /**
     * {@inheritdoc}
     */
    public function getDeterminant($string, $gender = false)
    {
        if (self::isFirstVowel($string)) :
            return __("l'", 'tify');
        else :
            return $gender ? __("la", 'tify') : __("le", 'tify');
        endif;
    }
}