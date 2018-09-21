<?php

namespace tiFy\Column;

class AbstractColumnDisplayPostTypeController extends AbstractColumnDisplayController
{
    /**
     * Affichage du contenu de la colonne
     *
     * @param string $column_name Identifiant de qualification de la colonne.
     * @param int $post_id Identifiant du post.
     * @param null $var3 Argument homis dans ce contexte.
     *
     * @return string
     */
    public function content($column_name, $post_id, $var3 = null)
    {
        parent::content($column_name, $post_id, $var3);
    }
}