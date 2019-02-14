<?php

namespace tiFy\Contracts\Taxonomy;

use tiFy\Contracts\Kernel\ParamsBag;

interface TaxonomyFactory extends ParamsBag
{
    /**
     * Récupération du nom de qualification du type de post.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération d'un intitulé.
     *
     * @param string $key Clé d'indice de l'intitulé.
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy
     * plural|singular|name|singular_name|menu_name|all_items|edit_item|view_item|update_item|add_new_item|
     * new_item_name|parent_item|parent_item_colon|search_items|popular_items|separate_items_with_commas|
     * add_or_remove_items|choose_from_most_used|not_found|back_to_items
     * @param string $default Valeur de retour par défaut.
     *
     * @return string
     */
    public function label(string $key, string $default = '') : string;
}