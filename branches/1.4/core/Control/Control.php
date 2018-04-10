<?php

namespace tiFy\Core\Control;

use tiFy\App\Core;
use tiFy\Core\Control\AccordionMenu\AccordionMenu;
use tiFy\Core\Control\AdminPanel\AdminPanel;
use tiFy\Core\Control\Calendar\Calendar;
use tiFy\Core\Control\Checkbox\Checkbox;
use tiFy\Core\Control\Colorpicker\Colorpicker;
use tiFy\Core\Control\CookieNotice\CookieNotice;
use tiFy\Core\Control\CryptedData\CryptedData;
use tiFy\Core\Control\CurtainMenu\CurtainMenu;
use tiFy\Core\Control\DropdownColors\DropdownColors;
use tiFy\Core\Control\DropdownGlyphs\DropdownGlyphs;
use tiFy\Core\Control\DropdownImages\DropdownImages;
use tiFy\Core\Control\DropdownMenu\DropdownMenu;
use tiFy\Core\Control\Findposts\Findposts;
use tiFy\Core\Control\HolderImage\HolderImage;
use tiFy\Core\Control\ImageLightbox\ImageLightbox;
use tiFy\Core\Control\MediaFile\MediaFile;
use tiFy\Core\Control\MediaImage\MediaImage;
use tiFy\Core\Control\Modal\Modal;
use tiFy\Core\Control\Notices\Notices;
use tiFy\Core\Control\Progress\Progress;
use tiFy\Core\Control\Repeater\Repeater;
use tiFy\Core\Control\ScrollPaginate\ScrollPaginate;
use tiFy\Core\Control\SlickCarousel\SlickCarousel;
use tiFy\Core\Control\Slider\Slider;
use tiFy\Core\Control\Spinkit\Spinkit;
use tiFy\Core\Control\Suggest\Suggest;
use tiFy\Core\Control\Tabs\Tabs;
use tiFy\Core\Control\TextRemaining\TextRemaining;

/**
 * Class Control
 *
 * @method static AccordionMenu(array $attrs = [])
 * @method static AdminPanel(array $attrs = [])
 * @method static Calendar(array $attrs = [])
 * @method static Checkbox(array $attrs = [])
 * @method static Colorpicker(array $attrs = [])
 * @method static CookieNotice(array $attrs = [])
 * @method static CryptedData(array $attrs = [])
 * @method static CurtainMenu(array $attrs = [])
 * @method static DropdownColors(array $attrs = [])
 * @method static DropdownGlyphs(array $attrs = [])
 * @method static DropdownImages(array $attrs = [])
 * @method static DropdownMenu(array $attrs = [])
 * @method static Findposts(array $attrs = [])
 * @method static HolderImage(array $attrs = [])
 * @method static ImageLightbox(array $attrs = [])
 * @method static MediaFile(array $attrs = [])
 * @method static MediaImage(array $attrs = [])
 * @method static Modal(array $attrs = [])
 * @method static Notices(array $attrs = [])
 * @method static Progress(array $attrs = [])
 * @method static Repeater(array $attrs = [])
 * @method static ScrollPaginate(array $attrs = [])
 * @method static SlickCarousel(array $attrs = [])
 * @method static Slider(array $attrs = [])
 * @method static Spinkit(array $attrs = [])
 * @method static Suggest(array $attrs = [])
 * @method static Table(array $attrs = [])
 * @method static Tabs(array $attrs = [])
 * @method static TextRemaining(array $attrs = [])
 */
class Control extends Core
{
    /**
     * Liste des classes de rappel des controleurs
     * @var Factory[]
     */ 
    public static $Factory = [];

    /**
     * CONSTRUCTEUR
     */
    public function __construct()
    {
        parent::__construct();

        // Déclaration des controleurs d'affichage natifs
        foreach(glob(self::tFyAppDirname() . '/*/', GLOB_ONLYDIR) as $filename) :
            $id = basename($filename);

            self::register($id, "tiFy\\Core\\Control\\{$id}\\{$id}");
        endforeach;

        // Déclaration des controleurs d'affichage natifs dépréciés
        foreach(glob(self::tFyAppRootDirname() . '/bin/deprecated/app/core/Control/*/', GLOB_ONLYDIR) as $filename) :
            $id = basename($filename);

            self::register($id, "tiFy\\Core\\Control\\{$id}\\{$id}");
        endforeach;

        // Déclaration des événement de déclenchement
        $this->tFyAppAddAction('init');
    }

    /**
     * DECLENCHEURS
     */
    /**
     * Initialisation globale
     *
     * @return void
     */
    public function init()
    {
        // Déclaration des controleurs d'affichage personnalisés
        do_action('tify_control_register');

        // Auto-chargement de l'initialisation globale des champs
        foreach (self::$Factory as $id => $instance) :
            if (!$classname = get_class($instance)) :
                continue;
            endif;

            // Définition des classes d'aide à la saisie
            $_id = join('_', array_map('lcfirst', preg_split('#(?=[A-Z])#', $id)));

            $instance->addIncreaseHelper('tify_control' . $_id, 'display');

            if (is_callable([$classname, 'init'])) :
                call_user_func([$classname, 'init']);
            endif;
        endforeach;
    }

    /**
     * CONTROLEURS
     */
    /**
     * Déclaration d'un controleur d'affichage
     *
     * @param string $id Identifiant de qualification du controleur
     * @param string $callback classes ou méthodes ou fonctions de rappel
     *
     * @return null|object|callable|Factory
     */
    final public static function register($id, $callback)
    {
        if (class_exists($callback)) :
            return self::$Factory[$id] = self::loadOverride($callback);
        else :
            return self::$Factory[$id] = (string)$callback;
        endif;

    }

    /**
     * Affichage ou récupération du contenu d'un controleur natif
     *
     * @param string $name Identifiant de qualification du controleur d'affichage
     * @param array $args {
     *      Liste des attributs de configuration
     *
     *      @var array $attrs Attributs de configuration du champ
     *      @var bool $echo Activation de l'affichage du champ
     *
     * @return null|callable
     * @return AccordionMenu|AdminPanel|Calendar|Checkbox|Colorpicker|CookieNotice|CryptedData
     * @return CurtainMenu|DropdownColors|DropdownGlyphs|DropdownImages|DropdownMenu
     * @return Findposts|HolderImage|ImageLightbox|MediaFile|MediaImage|Modal|Notices|Progress
     * @return Repeater|ScrollPaginate|SlickCarousel|Slider|Spinkit|Suggest|Tabs|TextRemaining
     */
    final public static function __callStatic($id, $args)
    {
        if (!isset(static::$Factory[$id])) :
            return trigger_error(sprintf(__('Le controleur d\'affichage %s n\'est pas disponible.', 'tify'), $id));
        elseif ($classname = get_class(static::$Factory[$id])) :
            $callable = [$classname, 'display'];

            if (!isset($args[0])) :
                $args[0] = [];
            endif;
            $args[1] = isset($args[1]) ? $args[1] : false;
        else :
            $callable = static::$Factory[$id];
        endif;

        if (!is_callable($callable)) :
            return trigger_error(sprintf(__('La méthode d\'affichage du controleur d\'affichage %s ne peut être appelée.', 'tify'), $id));
        endif;

        return call_user_func_array($callable, $args);
    }

    /**
     * Appel d'une méthode helper de contrôleur
     *
     * @param string $id Identifiant de qualification du controleur
     * @param string $method Nom de qualification de la méthode à appeler
     *
     * @return null|static
     */
    final public static function call($id, $method)
    {
        $id = join('', array_map('ucfirst', preg_split('#_#', $id)));

        if (!isset(static::$Factory[$id])) :
            return null;
        endif;

        $classname = get_class(static::$Factory[$id]);

        if (!isset(static::$Factory[$id])) :
            return trigger_error(sprintf(__('Le controleur d\'affichage %s n\'est pas disponible.', 'tify'), $id));
        elseif (!$classname && ($method !== 'display')) :
            return trigger_error(sprintf(__('Le controleur d\'affichage %s n\'a pas de méthode %s disponible.', 'tify'), $id, $method));
        elseif ($classname) :
            $callable = [$classname, $method];
        else :
            $callable = static::$Factory[$id];
        endif;

        $args = array_slice(func_get_args(), 2);

        if (!is_callable($callable)) :
            return trigger_error(sprintf(__('Le controleur d\'affichage %s n\'a pas de méthode %s disponible.', 'tify'), $id, $method));
        endif;

        return call_user_func_array($callable, $args);
    }

    /**
     * Affichage d'un controleur
     *
     * @param string $name Nom de qualification du controleur d'affichage
     * @param array $args Liste des attributs de configuration
     *
     * @return static
     */
    final public static function display($name, $args = [], $echo = true)
    {
        return self::call($name, 'display', $args);
    }

    /**
     * Mise en file des scripts d'un controleur
     *
     * @param string $id Identifiant de qualification du controleur d'affichage
     * @param array $args Liste des attributs de configuration
     *
     * @return static
     */
    final public static function enqueue_scripts($id, $args = [])
    {
        return self::call($id, 'enqueue_scripts', $args);
    }
}