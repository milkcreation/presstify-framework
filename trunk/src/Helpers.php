<?php

use tiFy\tiFy;
use tiFy\Field\Field;
use tiFy\Form\Form;
use tiFy\Partial\Partial;
use tiFy\Route\Route;

/**
 * FIELD
 */
if (!function_exists('tify_field_button')) :
    /**
     * Bouton.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_button($attrs = [], $echo = true)
    {
        $output = (string)Field::Button($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_checkbox')) :
    /**
     * Case à cocher.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_checkbox($attrs = [], $echo = true)
    {
        $output = (string)Field::Checkbox($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_checkbox_collection')) :
    /**
     * Liste de cases à cocher.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_checkbox_collection($attrs = [], $echo = true)
    {
        $output = (string)Field::CheckboxCollection($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_colorpicker')) :
    /**
     * Selecteur de couleur.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_colorpicker($attrs = [], $echo = true)
    {
        $output = (string)Field::Colorpicker($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_crypted')) :
    /**
     * Champ crypté.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_crypted($attrs = [], $echo = true)
    {
        $output = (string)Field::Crypted($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_datetime_js')) :
    /**
     * Selecteur de date et heure JS.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_datetime_js($attrs = [], $echo = true)
    {
        $output = (string)Field::DatetimeJs($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_file')) :
    /**
     * Champ de téléversement de fichier.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_file($attrs = [], $echo = true)
    {
        $output = (string)Field::File($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_hidden')) :
    /**
     * Champ caché.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_hidden($attrs = [], $echo = true)
    {
        $output = (string)Field::Hidden($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_label')) :
    /**
     * Intitulé de champ.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_label($attrs = [], $echo = true)
    {
        $output = (string)Field::Label($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_media_file')) :
    /**
     * Fichier de la médiathèque.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_media_file($attrs = [], $echo = true)
    {
        $output = (string)Field::MediaFile($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_media_image')) :
    /**
     * Image de la médiathèque.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_media_image($attrs = [], $echo = true)
    {
        $output = (string)Field::MediaImage($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_number')) :
    /**
     * Nombre.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_number($attrs = [], $echo = true)
    {
        $output = (string)Field::Number($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_number_js')) :
    /**
     * Nombre dynamique.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_number_js($attrs = [], $echo = true)
    {
        $output = (string)Field::NumberJs($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_password')) :
    /**
     * Mot de passe.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_password($attrs = [], $echo = true)
    {
        $output = (string)Field::Password($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_radio')) :
    /**
     * Bouton radio.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_radio($attrs = [], $echo = true)
    {
        $output = (string)Field::Radio($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_radio_collection')) :
    /**
     * Liste de boutons radio.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_radio_collection($attrs = [], $echo = true)
    {
        $output = (string)Field::RadioCollection($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_repeater')) :
    /**
     * Répétiteur de champ.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_repeater($attrs = [], $echo = true)
    {
        $output = (string)Field::Repeater($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_select')) :
    /**
     * Selecteur.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_select($attrs = [], $echo = true)
    {
        $output = (string)Field::Select($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_select_js')) :
    /**
     * Selecteur dynamique et autocompletion.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_select_js($attrs = [], $echo = true)
    {
        $output = (string)Field::SelectJs($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_submit')) :
    /**
     * Soumission de formulaire.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_submit($attrs = [], $echo = true)
    {
        $output = (string)Field::Submit($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_text')) :
    /**
     * Champ de saisie.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_text($attrs = [], $echo = true)
    {
        $output = (string)Field::Text($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_text_remaining')) :
    /**
     * Champ de saisie limité.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_text_remaining($attrs = [], $echo = true)
    {
        $output = (string)Field::TextRemaining($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_textarea')) :
    /**
     * Zone de saisie libre.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_textarea($attrs = [], $echo = true)
    {
        $output = (string)Field::Textarea($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

if (!function_exists('tify_field_toggle_switch')) :
    /**
     * Bouton de bascule.
     *
     * @param array $attrs {
     *      Liste des attributs de configuration
     * }
     * @param bool $echo Activation de l'affichage. défaut true.
     *
     * @return string
     */
    function tify_field_toggle_switch($attrs = [], $echo = true)
    {
        $output = (string)Field::ToggleSwitch($attrs);

        if ($echo) :
            echo $output;
        else :
            return $output;
        endif;
    }
endif;

/**
 * FORM
 */
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

/**
 * Balise HTML
 *
 * @param array $attrs {
 *      Liste des attributs de configuration
 *
 * @var string $id Identifiant de qualification du controleur d'affichage.
 * @var string $tag Balise HTML div|span|a|... défaut div.
 * @var array $attrs Liste des attributs de balise HTML.
 * @var string $content Contenu de la balise HTML.
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

// --------------------------------------------------------------------------------------------------------------------------
/**
 * ROUTE
 */
/**
 * Indicateur de contexte de la requête principale.
 *
 * @return bool
 */
function is_route()
{
    return tiFy::instance()->serviceGet(Route::class)->is();
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
    return tiFy::instance()->serviceGet(Route::class)->exists($name);
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
    return tiFy::instance()->serviceGet(Route::class)->url($name, $replacements);
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
    return tiFy::instance()->serviceGet(Route::class)->redirect($name, $args, $status_code);
}

/**
 * Récupération du nom de qualification de la route courante à afficher.
 *
 * @return string
 */
function tify_route_current_name()
{
    return tiFy::instance()->serviceGet(Route::class)->currentName();
}

/**
 * Récupération des arguments de requête passés dans la route courante.
 *
 * @return array
 */
function tify_route_current_args()
{
    return tiFy::instance()->serviceGet(Route::class)->currentArgs();
}

/**
 * Vérifie si la page d'affichage courante correspond à une route déclarée
 *
 * @return bool
 */
function tify_route_has_current()
{
    return tiFy::instance()->serviceGet(Route::class)->hasCurrent();
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
    return tiFy::instance()->serviceGet(Route::class)->isCurrent($name);
}

// --------------------------------------------------------------------------------------------------------------------------
/* = CUSTOM TYPE = */
/** == Déclaration d'une taxonomie personnalisée == **/
function tify_custom_taxonomy_register($taxonomy, $args)
{
    tiFy\CustomType\CustomType::registerTaxonomy($taxonomy, $args);
}

/** == Déclaration d'un type de post personnalisé == **/
function tify_custom_post_type_register($post_type, $args)
{
    tiFy\CustomType\CustomType::registerPostType($post_type, $args);
}

// --------------------------------------------------------------------------------------------------------------------------
/* = DB = */
/** == Déclaration == **/
function tify_db_register($id, $args = [])
{
    return tiFy\Db\Db::register($id, $args);
}

/** == Déclaration == **/
function tify_db_get($id)
{
    return tiFy\Db\Db::get($id);
}

/** == Boucle == **/
/*** === Initialisation === ***/
function tify_query($id, $query = null)
{
    if ($db = tiFy\Db\Db::get($id)) {
        return $db->query($query);
    }
}

/*** === Récupération d'un champs == **/
function tify_query_field($name)
{
    if ($query = tiFy\Db\Db::$Query) {
        return $query->get_field($name);
    }
}

// --------------------------------------------------------------------------------------------------------------------------
/* = FILE UPLOAD = */
/** == Déclaration d'un fichier à télécharger == **/
function tify_upload_register($file)
{
    return tiFy\Upload\Upload::Register($file);
}

/** == Récupération du fichier à télécharger == **/
function tify_upload_get($type = null)
{
    return tiFy\Upload\Upload::Get($type);
}

/** == Url de téléchargement d'un fichier == **/
function tify_upload_url($file, $query_vars = [])
{
    return tiFy\Upload\Upload::Url($file, $query_vars);
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
    return tiFy\Medias\Download::url($media, $query_vars);
}

// --------------------------------------------------------------------------------------------------------------------------
/**
 * LOGIN
 */
/**
 * Déclaration
 * @deprecated \tiFy\Components\Login\README.md
 *
 * @param string $id Identifiant de qualification de l'interface d'authentification
 * @param string $callback Classe de rappel de l'interface d'authentification
 * @param array $attrs Attributs de configuration de l'interface d'authentification
 *
 * @return \tiFy\User\Login\Factory
 */
function tify_login_register($id, $callback, $attrs = [])
{
    return tiFy\User\Login\Login::register($id, $callback, $attrs);
}

/**
 * Affichage du formulaire d'authentification
 *
 * @param string $id Identifiant de qualification de l'interface d'authentification
 * @param array $attrs Attributs de configuration personnalisés
 * @param bool $echo Activation de l'affichage de la valeur de retour
 *
 * @return string
 */
function tify_login_form($id, $attrs = [], $echo = true)
{
    return tiFy\User\Login\Login::display($id, 'login_form', $attrs, $echo);
}

/**
 * Affichage des erreurs de traitement du formulaire d'authentification
 *
 * @param string $id Identifiant de qualification de l'interface d'authentification
 * @param array $attrs Attributs de configuration personnalisés
 * @param bool $echo Activation de l'affichage de la valeur de retour
 *
 * @return string
 */
function tify_login_form_errors($id, $attrs = [], $echo = true)
{
    return tiFy\User\Login\Login::display($id, 'login_form_errors', $attrs, $echo);
}

/**
 * Affichage du lien de déconnection
 *
 * @param string $id Identifiant de qualification de l'interface d'authentification
 * @param array $attrs Attributs de configuration personnalisés
 * @param bool $echo Activation de l'affichage de la valeur de retour
 *
 * @return string
 */
function tify_login_logout_link($id, $attrs = [], $echo = true)
{
    return tiFy\User\Login\Login::display($id, 'logout_link', $attrs, $echo);
}

// --------------------------------------------------------------------------------------------------------------------------
/* = MAIL = */
/** == Déclaration d'un email == **/
function tify_mail_register($id, $args = [])
{
    return \tiFy\Mail\Mail::register($id, $args);
}

/** == Récupération d'un email == **/
function tify_mail_get($id)
{
    return \tiFy\Mail\Mail::get($id);
}

// --------------------------------------------------------------------------------------------------------------------------
/* = META = */
/** == POST == **/
/** == Déclaration d'une metadonnée de post == **/
function tify_meta_post_register($post_type, $meta_key, $single = false, $sanitize_callback = 'wp_unslash')
{
    return tiFy\Metadata\Post::register($post_type, $meta_key, $single, $sanitize_callback);
}

/** == Récupération de métadonnée en mode avancée (gestion de l'ordre) == **/
function tify_meta_post_get($post_id, $meta_key)
{
    return tiFy\Metadata\Post::get($post_id, $meta_key);
}

/** == TERM == **/
/** == Déclaration d'une metadonnée de post == **/
function tify_meta_term_register($taxonomy, $meta_key, $single = false, $sanitize_callback = 'wp_unslash')
{
    return tiFy\Metadata\Term::Register($taxonomy, $meta_key, $single, $sanitize_callback);
}

/** == Récupération de métadonnée en mode avancée (gestion de l'ordre) == **/
function tify_meta_term_get($term_id, $meta_key)
{
    return tiFy\Metadata\Term::Get($term_id, $meta_key);
}

/** == USER == **/
/** == Déclaration d'une metadonnée de post == **/
function tify_meta_user_register($meta_key, $single = false, $sanitize_callback = 'wp_unslash')
{
    return tiFy\Metadata\User::Register($meta_key, $single, $sanitize_callback);
}

/** == Récupération de métadonnée en mode avancée (gestion de l'ordre) == **/
function tify_meta_user_get($user_id, $meta_key)
{
    return tiFy\Metadata\User::Get($user_id, $meta_key);
}

/** == Déclaration d'une metadonnée de post == **/
function tify_option_user_register($meta_key, $single = false, $sanitize_callback = 'wp_unslash')
{
    return tiFy\Metadata\UserOption::Register($meta_key, $single, $sanitize_callback);
}

/** == Récupération de métadonnée en mode avancée (gestion de l'ordre) == **/
function tify_option_user_get($user_id, $meta_key)
{
    return tiFy\Metadata\UserOption::Get($user_id, $meta_key);
}

// --------------------------------------------------------------------------------------------------------------------------
/* = OPTIONS = */
/** == Déclaration d'une section de boîte à onglets dans l'interface de gestion des options de PresstiFy  == **/
function tify_options_register_node($node = [])
{
    return tiFy\Options\Options::registerNode($node);
}

// --------------------------------------------------------------------------------------------------------------------------
/* = SCRIPT LOADER = */
/** == Déclaration / Modification d'un script JavaScript == **/
function tify_register_script($handle, $args = [])
{
    return tiFy\ScriptLoader\ScriptLoader::register_script($handle, $args);
}

/** == Déclaration / Modification d'un script JavaScript == **/
function tify_register_style($handle, $args = [])
{
    return tiFy\ScriptLoader\ScriptLoader::register_style($handle, $args);
}

/** == Récupération de la source d'un script JS == **/
function tify_script_get_src($handle, $context = null)
{
    return tiFy\ScriptLoader\ScriptLoader::get_src($handle, 'js', $context);
}

/** == Récupération de la source d'une feuille de style CSS == **/
function tify_style_get_src($handle, $context = null)
{
    return tiFy\ScriptLoader\ScriptLoader::get_src($handle, 'css', $context);
}

/** == Récupération de l'attribut d'un script JS == **/
function tify_script_get_attr($handle, $attr = 'version')
{
    return tiFy\ScriptLoader\ScriptLoader::get_attr($handle, 'js', $attr);
}

/** == Récupération de l'attribut d'une feuille de style CSS == **/
function tify_style_get_attr($handle, $attr = 'version')
{
    return tiFy\ScriptLoader\ScriptLoader::get_attr($handle, 'css', $attr);
}

// --------------------------------------------------------------------------------------------------------------------------
/* = TABOOX = */
/** == Déclaration d'une boîte à onglets ==    **/
function tify_taboox_register_box($hookname, $env, $args = [])
{
    return tiFy\Taboox\Taboox::registerBox($hookname, $env, $args);
}

/** == Déclaration d'une section de boîte à onglets == **/
function tify_taboox_register_node($hookname, $args = [])
{
    return tiFy\Taboox\Taboox::registerNode($hookname, $args);
}

/** == Affichage de la boîte à onglet de l'écran courant == **/
function tify_taboox_display()
{
    if (!$display = tiFy\Taboox\Taboox::display()) :
        echo 'Rien à voir';
        return;
    endif;

    echo $display->render(func_get_args());
}

// --------------------------------------------------------------------------------------------------------------------------
/* = TEMPLATES = */
function tify_templates_register($id, $attrs, $context)
{
    return tiFy\Templates\Templates::register($id, $attrs, $context);
}

function tify_templates_current()
{
    return tiFy\Templates\Templates::$Current;
}

// --------------------------------------------------------------------------------------------------------------------------
/* = ROUTER = */
/**
 * Déclaration d'une route
 *
 * @uses \tiFy\Router\Router::register()
 * @return \tiFy\Router\Factory
 */
function tify_router_register($id, $attrs = [])
{
    return tiFy\Router\Router::register($id, $attrs = []);
}

/**
 * Récupération de l'identifiant du contenu accroché à une route
 *
 * @uses \tiFy\Router\Router::get()
 * @return int
 */
function tify_router_get_selected($id)
{
    return tiFy\Router\Router::get($id)->getSelected();
}