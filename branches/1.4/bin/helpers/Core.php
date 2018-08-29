<?php

namespace
{
    use tiFy\Core\Layout\Layout;
    use tiFy\Core\Route\Route;

    /**
     * Indicateur de contexte de la requête principale.
     *
     * @return bool
     */
    function is_route()
    {
        return Route::is();
    }

    /**
     * Vérifie la correspondance du nom de qualification d'une route existante avec la valeur soumise.
     *
     * @param string $name Identifiant de qualification de la route à vérifier
     *
     * @return bool
     */
    function tify_route_exists($name)
    {
        return Route::exists($name);
    }

    /**
     * Récupération de l'url d'une route déclarée
     *
     * @param string $name Identifiant de qualification de la route
     * @param array $replacements Arguments de remplacement
     *
     * @return string
     */
    function tify_route_url($name, array $replacements = [])
    {
        return Route::url($name, $replacements);
    }

    /**
     * Redirection de page vers une route déclarée.
     *
     * @param string $name Identifiant de qualification de la route
     * @param array $args Liste arguments passés en variable de requête dans l'url
     * @param int $status_code Code de redirection. @see https://fr.wikipedia.org/wiki/Liste_des_codes_HTTP
     *
     * @return void
     */
    function tify_route_redirect($name, array $args = [], $status_code = 301)
    {
        Route::redirect($name, $args, $status_code);
    }

    /**
     * Récupération du nom de qualification de la route courante à afficher.
     *
     * @return string
     */
    function tify_route_current_name()
    {
        return Route::currentName();
    }

    /**
     * Récupération des arguments de requête passés dans la route courante.
     *
     * @return array
     */
    function tify_route_current_args()
    {
        Route::currentArgs();
    }

    /**
     * Vérifie si la page d'affichage courante correspond à une route déclarée
     *
     * @return bool
     */
    function tify_route_has_current()
    {
        Route::hasCurrent();
    }

    /**
     * Vérifie de correspondance du nom de qualification la route courante avec la valeur soumise.
     *
     * @param string $name Identifiant de qualification de la route à vérifier
     *
     * @return bool
     */
    function tify_route_is_current($name)
    {
        return Route::isCurrent($name);
    }

    /**
     * Fil d'arianne
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_layout_breadcrumb($attrs = [], $echo = true)
    {
        $layout = (string)Layout::Breadcrumb($attrs);

        if ($echo) :
            echo $layout;
        else :
            return $layout;
        endif;
    }

    /**
     * Titre de balise meta
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_layout_meta_title($attrs = [], $echo = true)
    {
        $layout = (string)Layout::MetaTitle($attrs);

        if ($echo) :
            echo $layout;
        else :
            return $layout;
        endif;
    }

    /**
     * Message de notification
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     *
     *      @var string $id Identifiant de qualification du controleur d'affichage.
     *      @var string $container_id ID HTML du conteneur de l'élé@var string $id Identifiant de qualification du controleur d'affichage.ment.
     *      @var string $container_class Classes HTML du conteneur de l'élément.
     *      @var string $text Texte de notification. défaut 'Lorem ipsum dolor site amet'.
     *      @var string $dismissible Bouton de masquage de la notification.
     *      @var string $type Type de notification info|warning|success|error. défaut info.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_layout_notice($attrs = [], $echo = true)
    {
        $layout = (string)Layout::Notice($attrs);

        if ($echo) :
            echo $layout;
        else :
            return $layout;
        endif;
    }

    /**
     * Balise HTML
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     *
     *      @var string $id Identifiant de qualification du controleur d'affichage.
     *      @var string $tag Balise HTML div|span|a|... défaut div.
     *      @var array $attrs Liste des attributs de balise HTML.
     *      @var string $content Contenu de la balise HTML.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_layout_tag($attrs = [], $echo = true)
    {
        $layout = (string)Layout::Tag($attrs);

        if ($echo) :
            echo $layout;
        else :
            return $layout;
        endif;
    }


    // --------------------------------------------------------------------------------------------------------------------------
    /* = CLASS AUTOLOADER = */
    /** == Chargement automatique des classe PHP == **/
    function tify_class_loader( $namespace, $base_dir, $bootstrap = null )
    {
        return tiFy\tiFy::classLoad( $namespace, $base_dir, $bootstrap );
    }
    
    // --------------------------------------------------------------------------------------------------------------------------
    /* = CONTROL = */
    /** == Mise en file des scripts du contrôleur == **/
    function tify_control_enqueue($scripts)
    {
        if(is_string($scripts)) :
            $scripts = [$scripts];
        endif;

        foreach($scripts as $script) :
            // Rétrocompatibilité
            if ($script === 'switch') :
                $script = 'switcher';
            endif;
            tiFy\Core\Control\Control::enqueue_scripts($script);
        endforeach;
    }

    // --------------------------------------------------------------------------------------------------------------------------
    /* = CUSTOM TYPE = */
    /** == Déclaration d'une taxonomie personnalisée == **/
    function tify_custom_taxonomy_register( $taxonomy, $args )
    {
        tiFy\Core\CustomType\CustomType::registerTaxonomy( $taxonomy, $args );
    }

    /** == Déclaration d'un type de post personnalisé == **/
    function tify_custom_post_type_register( $post_type, $args )
    {
        tiFy\Core\CustomType\CustomType::registerPostType( $post_type, $args );
    }

    // --------------------------------------------------------------------------------------------------------------------------
    /* = DB = */
    /** == Déclaration == **/
    function tify_db_register( $id, $args = array() )
    {
        return tiFy\Core\Db\Db::register( $id, $args );
    }
    
    /** == Déclaration == **/
    function tify_db_get( $id )
    {
        return tiFy\Core\Db\Db::get( $id );
    }
    
    /** == Boucle == **/
    /*** === Initialisation === ***/
    function tify_query( $id, $query = null )
    {
        if( $db = tiFy\Core\Db\Db::get( $id ) )
            return $db->query( $query );
    }

    /*** === Récupération d'un champs == **/
    function  tify_query_field( $name )
    {
        if( $query = tiFy\Core\Db\Db::$Query )
            return $query->get_field( $name );
    }

    // --------------------------------------------------------------------------------------------------------------------------
    /* = FILE UPLOAD = */
    /** == Déclaration d'un fichier à télécharger == **/
    function tify_upload_register( $file )
    {
        return tiFy\Core\Upload\Upload::Register( $file );
    }
    
    /** == Récupération du fichier à télécharger == **/
    function tify_upload_get( $type = null )
    {
        return tiFy\Core\Upload\Upload::Get( $type );
    }
    
    /** == Url de téléchargement d'un fichier == **/
    function tify_upload_url( $file, $query_vars = array() )
    {
        return tiFy\Core\Upload\Upload::Url( $file, $query_vars );
    }

    /**
     * Url de téléchargement d'un fichier média
     *
     * @param string|int $file Chemin relatif|Chemin absolue|Url|Identifiant d'un fichier de la médiathèque
     * @param array $additional_query_vars Arguments de requête complémentaires
     *
     * @return string
     */
    function tify_medias_download_url($media, $query_vars = [])
    {
        return tiFy\Core\Medias\Download::url($media, $query_vars);
    }

    // --------------------------------------------------------------------------------------------------------------------------
    /* = FORMS = */
    /** == Affichage d'un formulaire == **/
    function tify_form_display( $form = null, $echo = true )
    {
        if ($echo) :
            echo do_shortcode('[formulaire id="'. $form .'"]');
        else :
            return do_shortcode('[formulaire id="'. $form .'"]');
        endif;
    }
    
    /** == Déclaration d'un formulaire == **/
    function tify_form_register( $id = null, $attrs = array() )
    {
        // Deprecated
        if( is_array( $id ) ) :
            $attrs = $id;
            $id = $attrs['ID'];
            _deprecated_argument( __FUNCTION__, '1.1.160923', sprintf( __( 'La déclaration du formulaire %s n\'est conforme, elle doit désormais contenir un identifiant et des attributs.', 'tify' ), $id ) );    
        endif;
        
        return tiFy\Core\Forms\Forms::register( $id, $attrs );
    }
    
    /** == Déclaration d'un addon == **/
    function tify_form_register_addon( $id, $callback, $args = array() )
    {
        return tiFy\Core\Forms\Addons::register( $id, $callback, $args );
    }
    
    /** == Shortcode d'affichage de formulaire == **/
    function tify_form_shortcode( $atts = array() )
    {
        extract(
            shortcode_atts(
                array( 'id' => null ),
                $atts
            )
        );

        return tiFy\Core\Forms\Forms::display( $id, false );
    }
    
    /** == Définition du formulaire courant == **/
    function tify_form_set_current( $form_id )
    {
        return tiFy\Core\Forms\Forms::setCurrent( $form_id );
    }
    
    /** == Récupération du formulaire courant == **/
    function tify_form_get_current()
    {
        return tiFy\Core\Forms\Forms::getCurrent();
    }
    
    /** == Récupération d'un formulaire == **/
    function tify_form_get( $id )
    {
        return tiFy\Core\Forms\Forms::get( $id );
    }
    
    // --------------------------------------------------------------------------------------------------------------------------
    /* = MAIL = */
    /** == Déclaration d'un email == **/
    function tify_mail_register( $id, $args = array() )
    {
        return \tiFy\Core\Mail\Mail::register( $id, $args );
    }
    
    /** == Récupération d'un email == **/
    function tify_mail_get( $id )
    {
        return \tiFy\Core\Mail\Mail::get( $id );
    }    
    
    // --------------------------------------------------------------------------------------------------------------------------    
    /* = META = */
    /** == POST == **/
    /** == Déclaration d'une metadonnée de post == **/
    function tify_meta_post_register( $post_type, $meta_key, $single = false, $sanitize_callback = 'wp_unslash' )
    {
        return tiFy\Core\Meta\Post::register( $post_type, $meta_key, $single, $sanitize_callback );
    }
    
    /** == Récupération de métadonnée en mode avancée (gestion de l'ordre) == **/
    function tify_meta_post_get( $post_id, $meta_key )
    {
        return tiFy\Core\Meta\Post::get( $post_id, $meta_key );
    }
    
    /** == TERM == **/
    /** == Déclaration d'une metadonnée de post == **/
    function tify_meta_term_register( $taxonomy, $meta_key, $single = false, $sanitize_callback = 'wp_unslash' )
    {
        return tiFy\Core\Meta\Term::Register( $taxonomy, $meta_key, $single, $sanitize_callback );    
    }
    
    /** == Récupération de métadonnée en mode avancée (gestion de l'ordre) == **/
    function tify_meta_term_get( $term_id, $meta_key )
    {
        return tiFy\Core\Meta\Term::Get( $term_id, $meta_key );    
    }
    
    /** == USER == **/
    /** == Déclaration d'une metadonnée de post == **/
    function tify_meta_user_register( $meta_key, $single = false, $sanitize_callback = 'wp_unslash' )
    {
        return tiFy\Core\Meta\User::Register( $meta_key, $single, $sanitize_callback );    
    }
    
    /** == Récupération de métadonnée en mode avancée (gestion de l'ordre) == **/
    function tify_meta_user_get( $user_id, $meta_key )
    {
        return tiFy\Core\Meta\User::Get( $user_id, $meta_key );    
    }
    
    /** == Déclaration d'une metadonnée de post == **/
    function tify_option_user_register( $meta_key, $single = false, $sanitize_callback = 'wp_unslash' )
    {
        return tiFy\Core\Meta\UserOption::Register( $meta_key, $single, $sanitize_callback );    
    }
    
    /** == Récupération de métadonnée en mode avancée (gestion de l'ordre) == **/
    function tify_option_user_get( $user_id, $meta_key )
    {
        return tiFy\Core\Meta\UserOption::Get( $user_id, $meta_key );    
    }
    
    // --------------------------------------------------------------------------------------------------------------------------    
    /* = OPTIONS = */
    /** == Déclaration d'une section de boîte à onglets dans l'interface de gestion des options de PresstiFy  == **/
    function tify_options_register_node( $node = array() )
    {
        return tiFy\Core\Options\Options::registerNode( $node );
    }
    
    // --------------------------------------------------------------------------------------------------------------------------
    /* = SCRIPT LOADER = */        
    /** == Déclaration / Modification d'un script JavaScript == **/
     function tify_register_script( $handle, $args = array() )
     {
         return tiFy\Core\ScriptLoader\ScriptLoader::register_script( $handle, $args );
    }
    
    /** == Déclaration / Modification d'un script JavaScript == **/
    function tify_register_style( $handle, $args = array() )
    {
         return tiFy\Core\ScriptLoader\ScriptLoader::register_style( $handle, $args );
    }
     
    /** == Récupération de la source d'un script JS == **/
    function tify_script_get_src( $handle, $context = null )
    {
        return tiFy\Core\ScriptLoader\ScriptLoader::get_src( $handle, 'js', $context );
    }
     
    /** == Récupération de la source d'une feuille de style CSS == **/
    function tify_style_get_src( $handle, $context = null )
    {
        return tiFy\Core\ScriptLoader\ScriptLoader::get_src( $handle, 'css', $context );
    }
     
    /** == Récupération de l'attribut d'un script JS == **/
    function tify_script_get_attr( $handle, $attr = 'version' )
    {
        return tiFy\Core\ScriptLoader\ScriptLoader::get_attr( $handle, 'js', $attr );
    }
     
    /** == Récupération de l'attribut d'une feuille de style CSS == **/
    function tify_style_get_attr( $handle, $attr = 'version' )
    {
        return tiFy\Core\ScriptLoader\ScriptLoader::get_attr( $handle, 'css', $attr );
    }
    
    // --------------------------------------------------------------------------------------------------------------------------
    /* = TABOOX = */    
    /** == Déclaration d'une boîte à onglets ==    **/
    function tify_taboox_register_box( $hookname, $env, $args = array() )
    {
        return tiFy\Core\Taboox\Taboox::registerBox( $hookname, $env, $args );
    }
    
    /** == Déclaration d'une section de boîte à onglets == **/
    function tify_taboox_register_node( $hookname, $args = array() )
    {
        return tiFy\Core\Taboox\Taboox::registerNode($hookname, $args);
    }
        
    /** == Affichage de la boîte à onglet de l'écran courant == **/
    function tify_taboox_display()
    {
        if (!$display = tiFy\Core\Taboox\Taboox::display()) :
            echo 'Rien à voir';
            return;
        endif;

        echo $display->render(func_get_args());
    }
    
    // --------------------------------------------------------------------------------------------------------------------------
    /* = TEMPLATES = */    
    function tify_templates_register( $id, $attrs, $context )
    {
        return tiFy\Core\Templates\Templates::register( $id, $attrs, $context );
    }
    
    function tify_templates_current( )
    {
        return tiFy\Core\Templates\Templates::$Current;
    }

    // --------------------------------------------------------------------------------------------------------------------------
    /* = ROUTER = */
    /**
     * Déclaration d'une route
     *
     * @uses \tiFy\Core\Router\Router::register()
     * @return \tiFy\Core\Router\Factory
     */
    function tify_router_register($id, $attrs = [])
    {
        return tiFy\Core\Router\Router::register($id, $attrs = []);
    }

    /**
     * Récupération de l'identifiant du contenu accroché à une route
     *
     * @uses \tiFy\Core\Router\Router::get()
     * @return int
     */
    function tify_router_get_selected($id)
    {
        return tiFy\Core\Router\Router::get($id)->getSelected();
    }
}