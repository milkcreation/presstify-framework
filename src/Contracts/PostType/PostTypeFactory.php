<?php

namespace tiFy\Contracts\PostType;

use tiFy\Contracts\Kernel\ParamsBag;

interface PostTypeFactory extends ParamsBag
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
     * @see https://codex.wordpress.org/Function_Reference/register_post_type
     * plural|singular|name|singular_name|add_new|add_new_item|edit_item|new_item|view_item|view_items|search_items|
     * not_found|not_found_in_trash|parent_item_colon|all_items|archives|attributes|insert_into_item|
     * uploaded_to_this_item|featured_image|set_featured_image|remove_featured_image|use_featured_image|menu_name|
     * filter_items_list|items_list_navigation|items_list|name_admin_bar
     * @param string $default Valeur de retour par défaut.
     *
     * @return string
     */
    public function label(string $key, string $default = '') : string;
}