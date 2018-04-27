<?php

namespace tiFy\Core\MetaTag;

use Illuminate\Support\Arr;
use tiFy\App\Traits\App as TraitsApp;

class MetaTitle
{
    use TraitsApp;

    /**
     * Classe de rappel de la classe courante
     * @var static
     */
    protected static $instance;

    /**
     * Liste des éléments contenus dans le fil d'ariane.
     * @var array
     */
    protected $parts = [];

    /**
     * Liste des attributs de configuration
     * @var array
     */
    protected $attributes = [];

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    protected function __construct()
    {
        $this->attributes = $this->parse();
    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation
     *
     * @return void
     */
    private function __wakeup()
    {

    }

    /**
     * Initialisation
     *
     * @return self
     */
    final public static function make()
    {
        if (! self::$instance) :
            self::$instance = new static();
        endif;

        return self::$instance;
    }

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return array
     */
    public function parse($attrs = [])
    {
        $defaults = [
            'separator'       => ' | ',
            'parts'           => [],
        ];
        $attrs = array_merge($defaults, $attrs);

        if ($parts = $this->get('parts', [])) :
            $this->parts = $parts;
        endif;

        return $attrs;
    }

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'index de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = '')
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Récupération de la liste des éléments contenus dans le fil d'ariane.
     *
     * @return array
     */
    private function getPartList()
    {
        if (! $this->parts) :
            $this->parts = (new WpQueryMetaTitle())->getList();
        endif;

        return $this->parts;
    }

    /**
     * Ajout d'un élément de contenu au fil d'arianne.
     *
     * @return $this
     */
    final public function addPart($string)
    {
        $this->parts[] = $string;

        return $this;
    }

    /**
     * Supprime l'ensemble des éléments de contenu prédéfinis.
     *
     * @return $this
     */
    public function reset()
    {
        $this->parts = [];

        return $this;
    }

    /**
     * Affichage.
     *
     * @return string
     */
    final protected function display()
    {
        // Définition des arguments de template
        $separator = $this->get('separator');
        $parts = $this->getPartList();

        // Récupération du template d'affichage
        return implode($separator, $parts);
    }

    /**
     * Récupération de l'affichage du controleur
     *
     * @return string
     */
    final public function __toString()
    {
        return $this->display();
    }
}