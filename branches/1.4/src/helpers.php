<?php

//use tiFy\tiFy;
//use tiFy\Field\Field;
//use tiFy\Form\Form;
use tiFy\Kernel\Kernel;
use tiFy\Partial\Partial;
//use tiFy\Route\Route;

/**
 * KERNEL
 * ---------------------------------------------------------------------------------------------------------------------
 */
if (!function_exists('app')) :
    /**
     * App - Controleur de l'application.
     *
     * @return \tiFy\App\AppInterface|\tiFy\App\Container\Container
     */
    function app()
    {
        return Kernel::App();
    }
endif;

if (!function_exists('assets')) :
    /**
     * Assets - Controleur des assets.
     * @see \tiFy\Kernel\Assets\Assets
     *
     * @return string
     */
    function assets()
    {
        return Kernel::Assets();
    }
endif;

if (!function_exists('class_info')) :
    /**
     * ClassInfo - Controleur d'informations sur une classe.
     * @see \tiFy\Kernel\ClassInfo\ClassInfo
     *
     * @param string|object Nom complet ou instance de la classe.
     *
     * @return string
     */
    function class_info($class)
    {
        return Kernel::ClassInfo($class);
    }
endif;

if (!function_exists('class_loader')) :
    /**
     * ClassLoader - Controleur de déclaration d'espaces de nom et d'inclusion de fichier automatique.
     *
     * @return \tiFy\Kernel\Composer\ClassLoader
     */
    function class_loader()
    {
        return Kernel::ClassLoader();
    }
endif;

if (!function_exists('config')) :
    /**
     * Config - Controleur de configuration.
     * {@internal Si $key est null > Retourne la classe de rappel du controleur.}
     * {@internal Si $key est un tableau > Utilise le tableau en tant que liste des attributs de configuration à définir.}
     *
     * @param null|array|string Clé d'indice|Liste des attributs de configuration à définir.
     *
     * @return mixed|\tiFy\Kernel\Config\Config
     */
    function config($key = null, $default = null)
    {
        $factory = Kernel::Config();

        if (is_null($key)) :
            return $factory;
        endif;

        if (is_array($key)) :
            return $factory->set($key);
        endif;

        return $factory->get($key, $default);
    }
endif;

if (!function_exists('container')) :
    /**
     * Container - Controleur d'injection de dépendances.
     * {@internal Si $alias est null > Retourne la classe de rappel du controleur.}
     *
     * @param string $alias Nom de qualification du service à récupérer.
     *
     * @return \tiFy\Kernel\Container\Container
     */
    function container($alias = null)
    {
        $factory = Kernel::Container();

        if (is_null($alias)) :
            return $factory;
        endif;

        return $factory->get($alias);
    }
endif;

if (!function_exists('events')) :
    /**
     * Events - Controleur d'événements.
     *
     * @return \tiFy\Kernel\Events\Events
     */
    function events()
    {
        return Kernel::Events();
    }
endif;

if (!function_exists('logger')) :
    /**
     * Logger - Controleur de journalisation des actions.
     *
     * @return \tiFy\Kernel\Logger\Logger
     */
    function logger()
    {
        return Kernel::Logger();
    }
endif;

if (!function_exists('paths')) :
    /**
     * Paths - Controleur des chemins vers les répertoires de l'application.
     *
     * @return \tiFy\Kernel\Filesystem\Paths
     */
    function paths()
    {
        return Kernel::Paths();
    }
endif;

if (!function_exists('request')) :
    /**
     * Request - Controleur de traitement de la requête principal
     *
     * @return \tiFy\Kernel\Http\Request
     */
    function request()
    {
        return Kernel::Request();
    }
endif;

if (!function_exists('view')) :
    /**
     * View
     *
     * @return string|\tiFy\Kernel\Templates\EngineInterface
     */
    function view($view = null, $data = [])
    {
        $factory = Kernel::TemplatesEngine();

        if (func_num_args() === 0) :
            return $factory;
        endif;

        return $factory->make($view, $data);
    }
endif;

/**
 * FIELD
 * ---------------------------------------------------------------------------------------------------------------------
 */
if (!function_exists('tify_field_button')) :
    /**
     * Bouton.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_button($attrs = [], $echo = true)
    {
        $output = (string)Field::Button($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_checkbox')) :
    /**
     * Case à cocher.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_checkbox($attrs = [], $echo = true)
    {
        $output = (string)Field::Checkbox($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_checkbox_collection')) :
    /**
     * Liste de cases à cocher.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_checkbox_collection($attrs = [], $echo = true)
    {
        $output = (string)Field::CheckboxCollection($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_colorpicker')) :
    /**
     * Selecteur de couleur.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_colorpicker($attrs = [], $echo = true)
    {
        $output = (string)Field::Colorpicker($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_crypted')) :
    /**
     * Champ crypté.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_crypted($attrs = [], $echo = true)
    {
        $output = (string)Field::Crypted($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_datetime_js')) :
    /**
     * Selecteur de date et heure JS.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_datetime_js($attrs = [], $echo = true)
    {
        $output = (string)Field::DatetimeJs($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_file')) :
    /**
     * Champ de téléversement de fichier.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_file($attrs = [], $echo = true)
    {
        $output = (string)Field::File($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_hidden')) :
    /**
     * Champ caché.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_hidden($attrs = [], $echo = true)
    {
        $output = (string)Field::Hidden($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_label')) :
    /**
     * Intitulé de champ.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_label($attrs = [], $echo = true)
    {
        $output = (string)Field::Label($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_media_file')) :
    /**
     * Fichier de la médiathèque.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_media_file($attrs = [], $echo = true)
    {
        $output = (string)Field::MediaFile($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_media_image')) :
    /**
     * Image de la médiathèque.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_media_image($attrs = [], $echo = true)
    {
        $output = (string)Field::MediaImage($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_number')) :
    /**
     * Nombre.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_number($attrs = [], $echo = true)
    {
        $output = (string)Field::Number($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_number_js')) :
    /**
     * Nombre dynamique.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_number_js($attrs = [], $echo = true)
    {
        $output = (string)Field::NumberJs($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_password')) :
    /**
     * Mot de passe.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_password($attrs = [], $echo = true)
    {
        $output = (string)Field::Password($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_radio')) :
    /**
     * Bouton radio.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_radio($attrs = [], $echo = true)
    {
        $output = (string)Field::Radio($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_radio_collection')) :
    /**
     * Liste de boutons radio.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_radio_collection($attrs = [], $echo = true)
    {
        $output = (string)Field::RadioCollection($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_repeater')) :
    /**
     * Répétiteur de champ.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_repeater($attrs = [], $echo = true)
    {
        $output = (string)Field::Repeater($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_select')) :
    /**
     * Selecteur.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_select($attrs = [], $echo = true)
    {
        $output = (string)Field::Select($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_select_image')) :
    /**
     * Selecteur d'image.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_select_image($attrs = [], $echo = true)
    {
        $output = (string)Field::SelectImage($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_select_js')) :
    /**
     * Selecteur dynamique et autocompletion.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_select_js($attrs = [], $echo = true)
    {
        $output = (string)Field::SelectJs($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_submit')) :
    /**
     * Soumission de formulaire.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_submit($attrs = [], $echo = true)
    {
        $output = (string)Field::Submit($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_text')) :
    /**
     * Champ de saisie.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_text($attrs = [], $echo = true)
    {
        $output = (string)Field::Text($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_text_remaining')) :
    /**
     * Champ de saisie limité.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_text_remaining($attrs = [], $echo = true)
    {
        $output = (string)Field::TextRemaining($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_textarea')) :
    /**
     * Zone de saisie libre.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_textarea($attrs = [], $echo = true)
    {
        $output = (string)Field::Textarea($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

if (!function_exists('tify_field_toggle_switch')) :
    /**
     * Bouton de bascule.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string

    function tify_field_toggle_switch($attrs = [], $echo = true)
    {
        $output = (string)Field::ToggleSwitch($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }*/
endif;

/**
 * FORM
 * ---------------------------------------------------------------------------------------------------------------------
 */
if (!function_exists('tify_form_display')) :
    /**
     * Affichage d'un formulaire.
     *
     * @param string $name Nom de qualification du formulaire.
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_form_display($name, $echo = true)
    {
        if ($echo) :
            echo do_shortcode('[formulaire name="' . $name . '"]');
        else :
            return do_shortcode('[formulaire name="' . $name . '"]');
        endif;
    }
endif;

/**
 * PAGE HOOK
 * ---------------------------------------------------------------------------------------------------------------------
 */
if (!function_exists('tify_page_hook_is')) :
    /**
     * Vérification d'existance d'une page associée à l'identifiant de qualification de la route.
     *
     * @param string $name Identifiant de qualification de la route.
     * @param null|int|\WP_Post| $post Post Wordpress courant|Identifiant de qualification du post|Object Post Wordpress.
     *
     * @return bool
     */
    function tify_page_hook_is($name, $post = null)
    {
        return Router::get()->isContentHook($name, $post);
    }
endif;

if (!function_exists('tify_page_hook_get')) :
    /**
     * Récupération de la page associée à l'identifiant de qualification de la route.
     *
     * @param string $name Identifiant de qualification de la route.
     * @param int $default Valeur de retour par défaut.
     *
     * @return int
     */
    function tify_page_hook_get($name, $default = 0)
    {
        return Router::get()->getContentHook($name, $default);
    }
endif;

if (!function_exists('tify_page_hook_permalink')) :

    /**
     * Récupération de la page associée à l'identifiant de qualification de la route.
     *
     * @param string $name Identifiant de qualification de la route.
     *
     * @return int
     */
    function tify_page_hook_permalink($name)
    {
        return Router::get()->getContentHookPermalink($name);
    }
endif;

// ---------------------------------------------------------------------------------------------------------------------
/**
 * PARTIAL
 */
if (!function_exists('tify_partial_breadcrumb')) :
    /**
     * Fil d'arianne.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_partial_breadcrumb($attrs = [], $echo = true)
    {
        $output = (string)Partial::Breadcrumb($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_partial_cookie_notice')) :
    /**
     * Message de notification validée par enregistrement de cookie.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_partial_cookie_notice($attrs = [], $echo = true)
    {
        $output = (string)Partial::CookieNotice($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_partial_holder_image')) :
    /**
     * Image de remplacement.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_partial_holder_image($attrs = [], $echo = true)
    {
        $output = (string)Partial::HolderImage($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_partial_modal')) :
    /**
     * Fenêtre modale.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_partial_modal($attrs = [], $echo = true)
    {
        $output = (string)Partial::Modal($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_partial_modal_trigger')) :
    /**
     * Déclencheur de fenêtre modale.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_partial_modal_trigger($attrs = [], $echo = true)
    {
        $output = (string)Partial::ModalTrigger($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_partial_notice')) :
    /**
     * Message de notification
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     *
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_partial_notice($attrs = [], $echo = true)
    {
        $output = (string)Partial::Notice($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_partial_sidebar')) :
    /**
     * Barre latéral de navigation
     *
     * @var array $attrs {
     *      Liste des attributs de configuration.
     *
     * @var string $pos Position de l'interface left (default)|right.
     * @var string $initial Etat initial de l'interface closed (default)|opened.
     * @var string|int $width Largeur de l'interface en px ou en %. Si l'unité de valeur n'est pas renseignée l'unité par défault est le px.
     * @var string|int $min -width Largeur de la fenêtre du navigateur en px ou %, à partir de laquelle l'interface est active. Si l'unité de valeur n'est pas renseignée l'unité par défault est le px.
     * @var int $z -index Profondeur de champs.
     * @var bool $animated Activation de l'animation à l'ouverture et la fermeture.
     * @var bool|string $toggle Activation et contenu de bouton de bascule. Si la valeur booléene active ou desactive le bouton; la valeur chaîne de caractère active et affiche la chaîne ex : <span>X</span>.
     * @var bool $enqueue_scripts Mise en file automatique des scripts (dans tous les contextes).
     * @var array $nodes {
     *          Liste des greffons (node) Elements de menu.
     *
     * @var string $id Identifiant du greffon.
     * @var string $class Classe HTML du greffon.
     * @var string $content Contenu du greffon.
     * @var int $position Position du greffon.
     * @todo \tiFy\Lib\Nodes\Base ne gère pas encore la position.
     *      }
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_partial_sidebar($attrs = [], $echo = true)
    {
        $output = (string)Partial::Sidebar($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_partial_slider')) :
    /**
     * Diaporama.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_partial_slider($attrs = [], $echo = true)
    {
        $layout = (string)Partial::Slider($attrs);

        if ($echo) :
            echo $layout;
        else :
            return $layout;
        endif;
    }
endif;

if (!function_exists('tify_partial_spinner')) :
    /**
     * Indicateur de chargement
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     *
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_partial_spinner($attrs = [], $echo = true)
    {
        $output = (string)Partial::Spinner($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_partial_table')) :
    /**
     * Tableau basé sur des div.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration.
     *
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_partial_table($attrs = [], $echo = true)
    {
        $layout = (string)Partial::Table($attrs);

        if ($echo) :
            echo $layout;
        else :
            return $layout;
        endif;
    }
endif;

if (!function_exists('tify_partial_tag')) :
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
    function tify_partial_tag($attrs = [], $echo = true)
    {
        $layout = (string)Partial::Tag($attrs);

        if ($echo) :
            echo $layout;
        else :
            return $layout;
        endif;
    }
endif;

/**
 * ROUTE
 * ---------------------------------------------------------------------------------------------------------------------
 */
if (!function_exists('is_route')) :
    /**
     * Indicateur de contexte de la requête principale.
     *
     * @return bool

    function is_route()
    {
        return tiFy::instance()->get(Route::class)->is();
    }*/
endif;

if (!function_exists('tify_route_current_name')) :
    /**
     * Récupération du nom de qualification de la route courante à afficher.
     *
     * @return string

    function tify_route_current_name()
    {
        return tiFy::instance()->get(Route::class)->currentName();
    }*/
endif;

if (!function_exists('tify_route_current_args')) :
    /**
     * Récupération des arguments de requête passés dans la route courante.
     *
     * @return array

    function tify_route_current_args()
    {
        return tiFy::instance()->get(Route::class)->currentArgs();
    }*/
endif;

if (!function_exists('tify_route_exists')) :
    /**
     * Vérifie la correspondance du nom de qualification d'une route existante avec la valeur soumise.
     *
     * @param string $name Identifiant de qualification de la route à vérifier
     *
     * @return bool

    function tify_route_exists($name)
    {
        return tiFy::instance()->get(Route::class)->exists($name);
    }*/
endif;

if (!function_exists('tify_route_has_current')) :
    /**
     * Vérifie si la page d'affichage courante correspond à une route déclarée
     *
     * @return bool

    function tify_route_has_current()
    {
        return tiFy::instance()->get(Route::class)->hasCurrent();
    }*/
endif;

if (!function_exists('tify_route_is_current')) :
    /**
     * Vérifie de correspondance du nom de qualification la route courante avec la valeur soumise.
     *
     * @param string $name Identifiant de qualification de la route à vérifier
     *
     * @return bool

    function tify_route_is_current($name)
    {
        return tiFy::instance()->get(Route::class)->isCurrent($name);
    } */
endif;

if (!function_exists('tify_route_redirect')) :
    /**
     * Redirection de page vers une route déclarée.
     *
     * @param string $name Identifiant de qualification de la route
     * @param array $args Liste arguments passés en variable de requête dans l'url
     * @param int $status_code Code de redirection. @see https://fr.wikipedia.org/wiki/Liste_des_codes_HTTP
     *
     * @return void

    function tify_route_redirect($name, array $args = [], $status_code = 301)
    {
        return tiFy::instance()->get(Route::class)->redirect($name, $args, $status_code);
    }*/
endif;

if (!function_exists('tify_route_url')) :
    /**
     * Récupération de l'url d'une route déclarée
     *
     * @param string $name Identifiant de qualification de la route
     * @param array $replacements Arguments de remplacement
     *
     * @return string

    function tify_route_url($name, array $replacements = [])
    {
        return tiFy::instance()->get(Route::class)->url($name, $replacements);
    }*/
endif;