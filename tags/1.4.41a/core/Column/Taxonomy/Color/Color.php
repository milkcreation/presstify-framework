<?php

namespace tiFy\Core\Column\Taxonomy\Color;

use tiFy\Core\Column\ColumnTaxonomy;

class Color extends ColumnTaxonomy
{
    /**
     * Récupération de l'intitulé de la colonne
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->Title ? : __('Couleur', 'tify');
    }

    /**
     * Récupération de la position de la colonne dans la table
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->Position ? : 1;
    }

    /**
     * Affichage du contenu de la colonne.
     *
     * @param string $content Contenu de la colonne.
     * @param string $column_name Identification de la colonne.
     * @param int $term_id Identifiant de qualification du terme.
     *
     * @return void
     */
    public function content($content, $column_name, $term_id)
    {
        if ($color = get_term_meta($term_id, '_color', true)) :
            echo "<div style=\"width:80px;height:80px;display:block;border:solid 1px #CCC;background-color:#F4F4F4;position:relative;\"><div style=\"position:absolute;top:5px;right:5px;bottom:5px;left:5px;background-color:{$color}\"></div></div>";
        endif;
    }
}