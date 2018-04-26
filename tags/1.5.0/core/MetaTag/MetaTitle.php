<?php

namespace tiFy\Core\MetaTag;

class MetaTitle extends AbstractController
{
    /**
     * Liste des éléments contenus dans le fil d'ariane.
     * @var array
     */
    private static $parts = [];

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return array
     */
    final protected function parse($attrs = [])
    {
        $defaults = [
            'separator'       => ' | ',
            'parts'           => [],
        ];
        $attrs = array_merge($defaults, $attrs);

        if ($parts = $this->get('parts', [])) :
            self::$parts = $parts;
        endif;

        return $attrs;
    }

    /**
     * Récupération de la liste des éléments contenus dans le fil d'ariane.
     *
     * @return array
     */
    private function getPartList()
    {
        if (!self::$parts) :
            self::$parts = (new WpQueryMetaTitle())->getList();
        endif;

        return self::$parts;
    }

    /**
     * Ajout d'un élément de contenu au fil d'arianne.
     *
     * @return $this
     */
    final public function addPart($string)
    {
        self::$parts[] = $string;

        return $this;
    }

    /**
     * Supprime l'ensemble des éléments de contenu prédéfinis.
     *
     * @return $this
     */
    public function reset()
    {
        self::$parts = [];

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
}