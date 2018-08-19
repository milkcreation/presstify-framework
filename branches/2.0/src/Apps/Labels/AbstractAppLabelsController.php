<?php

namespace tiFy\Apps\Labels;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use tiFy\Apps\AppInterface;
use tiFy\Apps\Item\AbstractAppItemController;

abstract class AbstractAppLabelsController extends AbstractAppItemController implements AppLabelsInterface
{
    /**
     * Classe de rappel du controleur de l'application associée.
     * @var AppInterface
     */
    protected $app;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Indicateur de genre féminin.
     * @var bool
     */
    protected $gender = false;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param AppInterface $app  Classe de rappel du controleur de l'application.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], AppInterface $app)
    {
        $this->name = $name;

        parent::__construct($attrs, $app);
    }

    /**
     * @return string
     */
    public function defaultEditItem()
    {
        return sprintf(
            __('Éditer %s %s', 'tify'),
            $this->getDeterminant($this->getSingular(), $this->getGender()),
            $this->getSingular()
        );
    }

    /**
     * @return string
     */
    public function defaultDatasItem()
    {
        if (self::isFirstVowel($this->getSingular())) :
            $determinant = __('de l\'', 'tify');
        elseif ($this->getGender()) :
            $determinant = __('de la', 'tify');
        else :
            $determinant = __('du', 'tify');
        endif;

        return sprintf(__('Données %s %s', 'tify'), $determinant, $this->getSingular());
    }

    /**
     * {@inheritdoc}
     */
    public function getGender()
    {
        return $this->gender;
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
    public function getDeterminant($string, $gender = false)
    {
        if (self::isFirstVowel($string)) :
            return __("l'", 'tify');
        else :
            return $gender ? __("la", 'tify') : __("le", 'tify');
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlural()
    {
        return $this->get('plural');
    }

    /**
     * {@inheritdoc}
     */
    public function getSingular()
    {
        return $this->get('singular');
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
    public function parse($attrs = [])
    {
        $this->gender = Arr::get($attrs, 'gender', false);
        $this->set(
            'plural',
            Str::lower(
                Arr::get($attrs, 'plural', $this->getName())
            )
        );
        $this->set(
            'singular',
            Str::lower(
                Arr::get($attrs, 'singular', $this->getName())
            )
        );

        parent::parse($attrs);
    }
}