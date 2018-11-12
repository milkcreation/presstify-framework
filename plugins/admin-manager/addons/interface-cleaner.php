<?php
/*
Addon Name: Interface Cleaner
Addon URI: http://presstify.com/admin-manager/addons/interface-cleaner
Description: Nettoyage de l'interface d'administration Wordpress
Version: 1.150324
Author: Milkcreation
Author URI: http://milkcreation.fr
*/

class tiFy_admin_manager_interface_cleaner
{
    public    // Chemins
        $dir,
        $uri,
        $path,

        $options;

    /**
     * Initialisation
     */
    function __construct()
    {
        global $tiFy;

        $this->tiFy = $tiFy;
        // Définition des chemins
        $this->dir = dirname(__FILE__);
        $this->path = $this->tiFy->get_relative_path($this->dir);
        $this->uri = $this->tiFy->uri . $this->path;

        // Actions et Filtres Wordpress
        add_action('init', [$this, 'wp_init']);
        add_action('widgets_init', [$this, 'wp_widgets_init']);
        add_action('admin_menu', [$this, 'wp_admin_menu']);
        add_action('admin_init', [$this, 'wp_admin_init']);
        add_action('wp_before_admin_bar_render', [$this, 'wp_before_admin_bar_render']);
    }

    /**
     * Définition des options
     */
    function set_options()
    {
        $defaults = [
            'remove_menus'             => [
                'dashboard'  => false,
                'posts'      => false,
                'media'      => false,
                'pages'      => false,
                'comments'   => true,
                'appearence' => false,
                'plugins'    => false,
                'users'      => false,
                'tools'      => false,
                'settings'   => false
            ],
            'remove_dashboard_widgets' => [
                'right_now'       => false,
                'recent_comments' => true,
                'incoming_links'  => true,
                'plugins'         => true,
                'quick_press'     => false,
                'recent_drafts'   => true,
                'activity'        => true,
                'primary'         => true,
                'secondary'       => true
            ],
            'post_type_support'        => [
                'comments'      => false,
                'trackbacks'    => false,
                'custom-fields' => false,
                'category'      => false,
                'revisions'     => false,
                'post_tag'      => false
            ],
            'unregister_widgets'       => [
                'pages'           => true,
                'calendar'        => true,
                'archives'        => true,
                'links'           => true,
                'meta'            => true,
                'search'          => true,
                'text'            => true,
                'categories'      => true,
                'recent_posts'    => true,
                'recent_comments' => true,
                'rss'             => true,
                'tag_cloud'       => true,
                'nav_menu'        => true
            ],
            'admin_bar_render'         => [
                'wp_logo'        => false,
                'about'          => false,
                'wporg'          => false,
                'documentation'  => false,
                'support-forums' => false,
                'feedback'       => false,
                'site-name'      => false,
                'view-site'      => false,
                'updates'        => false,
                'comments'       => true,
                'new-content'    => false,
                'my-account'     => false
            ]
        ];

        $this->options = apply_filters('tify_interface_cleaner_options', []);

        foreach (['remove_menus', 'remove_dashboard_widgets', 'post_type_support', 'unregister_widgets', 'admin_bar_render'] as $item)
            if (isset($this->options[$item]))
                $this->options[$item] = wp_parse_args($this->options[$item], $defaults[$item]);
            else
                $this->options[$item] = $defaults[$item];
    }

    /**
     * ACTIONS ET FILTRES WORDPRESS
     */
    /**
     * Nettoyage du support des type de post
     */
    function wp_init()
    {
        // Definition des options
        $this->set_options();

        if (!$this->options['post_type_support']['comments']) :
            remove_post_type_support('post', 'comments');
            remove_post_type_support('page', 'comments');
            update_option('default_comment_status', 0);
        endif;

        if (!$this->options['post_type_support']['trackbacks']) :
            remove_post_type_support('post', 'trackbacks');
            remove_post_type_support('page', 'trackbacks');
        endif;

        if (!$this->options['post_type_support']['custom-fields']) :
            remove_post_type_support('post', 'custom-fields');
            remove_post_type_support('page', 'custom-fields');
        endif;

        if (!$this->options['post_type_support']['revisions']) :
            remove_post_type_support('post', 'revisions');
            remove_post_type_support('page', 'revisions');
        endif;

        global $wp_taxonomies;

        if (!$this->options['post_type_support']['category']):
            if (isset($wp_taxonomies['category']))
                $wp_taxonomies['category']->show_in_nav_menus = false;
            unregister_taxonomy_for_object_type('category', 'post');
        endif;
        if (!$this->options['post_type_support']['post_tag']) :
            if (isset($wp_taxonomies['post_tag']))
                $wp_taxonomies['post_tag']->show_in_nav_menus = false;
            unregister_taxonomy_for_object_type('post_tag', 'post');
        endif;
    }

    /**
     * Initialisation des widgets
     */
    function wp_widgets_init()
    {
        // Nettoyage de la liste des widgets du thèmes
        $excludes = ['WP_Widget_Pages',
            'WP_Widget_Calendar',
            'WP_Widget_Archives',
            'WP_Widget_Links',
            'WP_Widget_Meta',
            'WP_Widget_Search',
            'WP_Widget_Text',
            'WP_Widget_Categories',
            'WP_Widget_Recent_Posts',
            'WP_Widget_Recent_Comments',
            'WP_Widget_RSS',
            'WP_Widget_Tag_Cloud',
            'WP_Nav_Menu_Widget'
        ];
        foreach ($excludes as $exclude)
            unregister_widget($exclude);
    }

    /**
     * Initialisation du menu d'administration
     */
    function wp_admin_menu()
    {
        // Suppression des entrées de menu non utilisées
        if ($this->options['remove_menus']['dashboard'])
            remove_menu_page('index.php');                  //Dashboard
        if ($this->options['remove_menus']['posts'])
            remove_menu_page('edit.php');                   //Posts
        if ($this->options['remove_menus']['media'])
            remove_menu_page('upload.php');                 //Media
        if ($this->options['remove_menus']['pages'])
            remove_menu_page('edit.php?post_type=page');    //Pages
        if ($this->options['remove_menus']['comments'])
            remove_menu_page('edit-comments.php');          //Comments
        if ($this->options['remove_menus']['appearence'])
            remove_menu_page('themes.php');                 //Appearance
        if ($this->options['remove_menus']['plugins'])
            remove_menu_page('plugins.php');                //Plugins
        if ($this->options['remove_menus']['users'])
            remove_menu_page('users.php');                  //Users
        if ($this->options['remove_menus']['tools'])
            remove_menu_page('tools.php');                  //Tools
        if ($this->options['remove_menus']['settings'])
            remove_menu_page('options-general.php');        //Settings

        // Désactivation du sous menu de réglages des options des commentaires
        if ($this->options['remove_menus']['comments'])
            remove_submenu_page('options-general.php', 'options-discussion.php');

        if (!$this->tiFy->is_allowed_user()) :
            remove_submenu_page('themes.php', 'themes.php');
            global $submenu;
            unset($submenu['themes.php'][6]); // Customize

            remove_menu_page('tools.php');
            remove_submenu_page('options-general.php', 'options-general.php');
            remove_submenu_page('options-general.php', 'options-writing.php');
            remove_submenu_page('options-general.php', 'options-reading.php');
            remove_submenu_page('options-general.php', 'options-media.php');
            remove_submenu_page('options-general.php', 'options-permalink.php');
        endif;
    }

    /**
     * Initialisation de l'interface d'administration
     */
    function wp_admin_init()
    {
        // Nettoyage des metaboxes du tableau de bord
        foreach ($this->options['remove_dashboard_widgets'] as $widget => $bool)
            if ($bool)
                remove_meta_box('dashboard_' . $widget, 'dashboard', 'normal');
    }

    /**
     * Nettoyage de la barre d'admin
     */
    function wp_before_admin_bar_render()
    {
        global $wp_admin_bar;

        foreach ($this->options['admin_bar_render'] as $node => $bool)
            if ($bool)
                $wp_admin_bar->remove_node($node);

        if (is_multisite()) :
            /**
             * @var \WP_Site $site
             */
            foreach (get_sites() as $site) :
                $wp_admin_bar->remove_menu('blog-' . $site->blog_id . '-c');
            endforeach;
        endif;
    }

}

new tiFy_admin_manager_interface_cleaner;