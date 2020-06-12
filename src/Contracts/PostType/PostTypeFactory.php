<?php declare(strict_types=1);

namespace tiFy\Contracts\PostType;

use tiFy\Contracts\Support\ParamsBag;
use WP_Post_Type;

/**
 * @property-read string $label
 * @property-read object $labels
 * @property-read string $description
 * @property-read bool $public
 * @property-read bool $hierarchical
 * @property-read bool $exclude_from_search
 * @property-read bool $publicly_queryable
 * @property-read bool $show_ui
 * @property-read bool $show_in_menu
 * @property-read bool $show_in_nav_menus
 * @property-read bool $show_in_admin_bar
 * @property-read int $menu_position
 * @property-read string $menu_icon
 * @property-read string $capability_type
 * @property-read bool $map_meta_cap
 * @property-read string $register_meta_box_cb
 * @property-read array $taxonomies
 * @property-read bool|string $has_archive
 * @property-read string|bool $query_var
 * @property-read bool $can_export
 * @property-read bool $delete_with_user
 * @property-read bool $_builtin
 * @property-read string $_edit_link
 * @property-read object $cap
 * @property-read array|false $rewrite
 * @property-read array|bool $supports
 * @property-read bool $show_in_rest
 * @property-read string|bool $rest_base
 * @property-read string|bool $rest_controller_class
 * @property-read \WP_REST_Controller $rest_controller
 */
interface PostTypeFactory extends ParamsBag
{
    /**
     * Récupération des données de délégation du type de post Wordpress associée.
     *
     * @param int|string $key
     *
     * @return mixed
     */
    public function __get($key);

    /**
     * Résolution de sortie de la classe sous forme de chaîne de caractère.
     * {@internal Retourne le nom de qualification du type de post.}
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Récupération du nom de qualification du type de post.
     *
     * @return string
     */
    public function getName(): string;

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
    public function label(string $key, string $default = ''): string;

    /**
     * Définition de métadonnée de terme.
     *
     * @param string|array $key Indice de la métadonnée ou tableau indexé ou tableau associatif.
     * @param bool $single Indicateur de donnée unique. Valeur par défaut des déclarations par tableau indexé.
     *
     * @return static
     */
    public function meta($key, bool $single = true): PostTypeFactory;

    /**
     * Initialisation du controleur.
     *
     * @return static
     */
    public function prepare(): PostTypeFactory;

    /**
     * Définition de l'instance du gestionnaire de types de post.
     *
     * @param PostType $manager
     *
     * @return static
     */
    public function setManager(PostType $manager): PostTypeFactory;

    /**
     * Définition de l'instance de la taxonomie Wordpress associée.
     *
     * @param WP_Post_Type $post_type
     *
     * @return static
     */
    public function setWpPostType(WP_Post_Type $post_type): PostTypeFactory;
}