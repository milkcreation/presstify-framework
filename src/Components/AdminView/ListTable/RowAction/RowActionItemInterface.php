<?php

namespace tiFy\Components\AdminView\ListTable\RowAction;

interface RowActionItemInterface
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
     * Liste des attributs de configuration par défaut.
     *
     * @return array
     */
    public function defaults();

    /**
     * Récupération de l'identifiant de qualification de la clef de sécurisation d'une action sur un élément.
     *
     * @return string
     */
    public function getNonce();

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration personnalisés.
     *
     *      @var string $content Contenu du lien (chaîne de caractère ou éléments HTML).
     *      @var array $attrs Liste des attributs complémentaires de la balise du lien.
     *      @var array $query_args Tableau associatif des arguments passés en requête dans l'url du lien.
     *      @var bool|string $nonce Activation de la création de l'identifiant de qualification de la clef de sécurisation passé en requête dans l'url du lien ou identifiant de qualification de la clef de sécurisation.
     *      @var bool|string $referer Activation de l'argument de l'url de référence passée en requête dans l'url du lien.
     * }
     *
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