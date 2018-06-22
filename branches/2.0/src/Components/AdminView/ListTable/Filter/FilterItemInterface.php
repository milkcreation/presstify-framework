<?php

namespace tiFy\Components\AdminView\ListTable\Filter;

interface FilterItemInterface
{
    /**
     * Récupération de la liste des attributs de configuration.
     *
     * @return array
     */
    public function all();

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indice de l'attributs. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration personnalisés.
     *
     *      @var string $content Contenu du lien de vue filtrée (chaîne de caractère ou éléments HTML).
     *      @var array $attrs Liste des attributs de balise HTML.
     *      @var array $query_args Tableau associatif des arguments passés en requête dans l'url du lien de vue filtrée
     *      @var array $remove_query_args Tableau indexé des arguments supprimés de l'url de requête du lien de vue filtrée
     *      @var int $count_items Nombre d'élément correspondant à la vue filtrée
     *      @var bool $current Définie si la vue courante correspond à la vue filtrée
     *      @var bool $hide_empty Masque le lien si aucun élément ne correspond à la vue filtrée
     *      @var bool|string $show_count Affiche le nombre d'éléments correspondant dans le lien de la vue filtrée false|true|'(%d)' où %d correspond au nombre d'éléments
     * }
     * @return void
     */
    public function parse($attrs = []);

    /**
     * Définition d'un attribut de configuration.
     *
     * @param string $key Clé d'indice de l'attributs. Syntaxe à point permise.
     * @param mixed $value Valeur de de l'attribut.
     *
     * @return $this
     */
    public function set($key, $value);

    /**
     * Affichage.
     *
     * @return string
     */
    public function display();

    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString();
}