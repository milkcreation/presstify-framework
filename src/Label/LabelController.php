<?php

namespace tiFy\Label;

use Illuminate\Support\Str;
use tiFy\Apps\AppController;

class LabelController extends AppController
{
    /**
     * Nom de qualification du controleur
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
     * @param string $name Nom de qualification du controleur
     * @param array $attrs Liste des attributs de configuration
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name = $name;
        $this->plural = __('éléments', 'tify');
        $this->singular = __('élément', 'tify');
        $this->attributes = $this->parse($attrs);
    }

    /**
     * Traitement des intitulés.
     *
     * @param array $labels Liste des intitulés.
     *
     * @return array
     */
    protected function parse($labels = [])
    {
        if (isset($labels['plural'])) :
            $this->plural = Str::lower($labels['plural']);
        endif;

        if (isset($labels['singular'])) :
            $this->singular = Str::lower($labels['singular']);
        endif;

        if (isset($labels['gender'])) :
            $this->gender = (bool)$labels['gender'];
        endif;

        $defaults = [
            'singular'                   => $this->singular,
            'plural'                     => $this->plural,
            'name'                       => Str::ucfirst($this->plural),
            'singular_name'              => $this->singular,
            'menu_name'                  => _x(Str::ucfirst($this->plural), 'admin menu', 'tify'),
            'name_admin_bar'             => _x($this->singular, 'add new on admin bar', 'tify'),
            'add_new'                    => !$this->gender
                ? sprintf(__('Ajouter un %s', 'tify'), $this->singular)
                : sprintf(__('Ajouter une %s', 'tify'), $this->singular),
            'add_new_item'               => !$this->gender
                ? sprintf(__('Ajouter un %s', 'tify'), $this->singular)
                : sprintf(__('Ajouter une %s', 'tify'), $this->singular),
            'new_item'                   => !$this->gender
                ? sprintf(__('Créer un %s', 'tify'), $this->singular)
                : sprintf(__('Créer une %s', 'tify'), $this->singular),
            'edit_item'                  => $this->default_edit_item(),
            'view_item'                  => !$this->gender
                ? sprintf(__('Voir cet %s', 'tify'), $this->singular)
                : sprintf(__('Voir cette %s', 'tify'), $this->singular),
            'all_items'                  => !$this->gender
                ? sprintf(__('Tous les %s', 'tify'), $this->singular)
                : sprintf(__('Toutes les %s', 'tify'), $this->singular),
            'search_items'               => !$this->gender
                ? sprintf(__('Rechercher un %s', 'tify'), $this->singular)
                : sprintf(__('Rechercher une %s', 'tify'), $this->singular),
            'parent_item_colon'          => !$this->gender
                ? sprintf(__('%s parent', 'tify'), Str::ucfirst($this->singular))
                : sprintf(__('%s parent', 'tify'), Str::ucfirst($this->singular)),
            'not_found'                  => !$this->gender
                ? sprintf(__('Aucun %s trouvé', 'tify'), Str::ucfirst($this->singular))
                : sprintf(__('Aucune %s trouvée', 'tify'), Str::ucfirst($this->singular)),
            'not_found_in_trash'         => !$this->gender
                ? sprintf(__('Aucun %s dans la corbeille', 'tify'), Str::ucfirst($this->singular))
                : sprintf(__('Aucune %s dans la corbeille', 'tify'), Str::ucfirst($this->singular)),
            'update_item'                => !$this->gender
                ? sprintf(__('Mettre à jour ce %s', 'tify'), $this->singular)
                : sprintf(__('Mettre à jour cette %s', 'tify'), $this->singular),
            'new_item_name'              => !$this->gender
                ? sprintf(__('Créer un %s', 'tify'), $this->singular)
                : sprintf(__('Créer une %s', 'tify'), $this->singular),
            'popular_items'              => sprintf(__('%s populaires', 'tify'), Str::ucfirst($this->plural)),
            'separate_items_with_commas' => sprintf(__('Séparer les %s par une virgule', 'tify'), $this->plural),
            'add_or_remove_items'        => sprintf(__('Ajouter ou supprimer des %s', 'tify'), $this->plural),
            'choose_from_most_used'      => !$this->gender
                ? sprintf(__('Choisir parmi les %s les plus utilisés', 'tify'), $this->plural)
                : sprintf(__('Choisir parmi les %s les plus utilisées', 'tify'),$this->plural),
            'datas_item'                 => $this->default_datas_item(),
            'import_items'               => sprintf(__('Importer des %s', 'tify'), $this->plural),
            'export_items'               => sprintf(__('Export des %s', 'tify'), $this->plural)
        ];

        return array_merge($defaults, $labels);
    }

    /**
     * @return string
     */
    public function default_edit_item()
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
    public function default_datas_item()
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
     * @param $label
     * @param string $value
     */
    public function set($label, $value = '')
    {
        $this->Labels[$label] = $value;
    }

    /**
     * @param null $label
     * @param string $default
     *
     * @return array|mixed|string
     */
    public function get($label = null, $default = '')
    {
        if (!$label) :
            return $this->Labels;
        elseif (isset($this->Labels[$label])) :

            return $this->Labels[$label];
        endif;

        return $default;
    }

    /**
     * Mise en majuscule de la première lettre d'une chaîne de caractère, même si celle-ci contient un accent
     *
     * @param string $string Chaîne de caractère à traiter
     *
     * @return string
     */
    final public static function ucFirst($string)
    {
        return Str::ucfirst($string);
    }

    /**
     * Permet de vérifier si la première lettre d'une chaîne de caractère est une voyelle
     *
     * @param string $string Chaîne de caractère à traiter
     *
     * @return string
     */
    final public static function isFirstVowel($string)
    {
        $first = strtolower(mb_substr(\remove_accents($string), 0, 1));

        return (in_array($first, ['a', 'e', 'i', 'o', 'u', 'y']));
    }

    /**
     * Récupération du déterminant de qualification d'une chaîne de caractère
     *
     * @param string $string Chaîne de caractère à traiter
     * @param bool $gender Genre de la chaîne de caractère à traiter (false : masculin, true : féminin)
     *
     * @return string
     */
    private function getDeterminant($string, $gender = false)
    {
        if (self::isFirstVowel($string)) :
            return __("l'", 'tify');
        else :
            return $gender ? __("la", 'tify') : __("le", 'tify');
        endif;
    }
}