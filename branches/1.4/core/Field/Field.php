<?php

/**
 * @name Field
 * @desc Gestion de controleurs d'affichage
 * @package presstiFy
 * @namespace \tiFy\Core\Field
 * @version 1.1
 * @subpackage Core
 * @since 1.2.596
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Core\Field;

use tiFy\App\Core;
use tiFy\Components\Fields\Button\Button;
use tiFy\Components\Fields\Checkbox\Checkbox;
use tiFy\Components\Fields\DatetimeJs\DatetimeJs;
use tiFy\Components\Fields\File\File;
use tiFy\Components\Fields\Hidden\Hidden;
use tiFy\Components\Fields\Label\Label;
use tiFy\Components\Fields\Number\Number;
use tiFy\Components\Fields\NumberJs\NumberJs;
use tiFy\Components\Fields\Password\Password;
use tiFy\Components\Fields\Radio\Radio;
use tiFy\Components\Fields\Repeater\Repeater;
use tiFy\Components\Fields\Select\Select;
use tiFy\Components\Fields\SelectJs\SelectJs;
use tiFy\Components\Fields\Submit\Submit;
use tiFy\Components\Fields\Text\Text;
use tiFy\Components\Fields\Textarea\Textarea;
use tiFy\Components\Fields\ToggleSwitch\ToggleSwitch;

/**
 * Class Field
 * @package tiFy\Core\Field
 *
 * @method static Button(string $id = null, array $attrs = [])
 * @method static Checkbox(string $id = null, array $attrs = [])
 * @method static DatetimeJs(string $id = null, array $attrs = [])
 * @method static File(string $id = null, array $attrs = [])
 * @method static Hidden(string $id = null, array $attrs = [])
 * @method static Label(string $id = null, array $attrs = [])
 * @method static Number(string $id = null, array $attrs = [])
 * @method static NumberJs(string $id = null, array $attrs = [])
 * @method static Password(string $id = null, array $attrs = [])
 * @method static Radio(string $id = null, array $attrs = [])
 * @method static Repeater(string $id = null, array $attrs = [])
 * @method static Select(string $id = null, array $attrs = [])
 * @method static SelectJs(string $id = null, array $attrs = [])
 * @method static Submit(string $id = null, array $attrs = [])
 * @method static Text(string $id = null, array $attrs = [])
 * @method static Textarea(string $id = null, array $attrs = [])
 * @method static ToggleSwitch(string $id = null, array $attrs = [])
 */
class Field extends Core
{
    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Déclaration des controleurs d'affichage natifs
        foreach(glob($this->appAbsDir() . '/components/Fields/*/', GLOB_ONLYDIR) as $filename) :
            $name = basename($filename);

            self::register($name, "tiFy\\Components\\Fields\\{$name}\\{$name}::make");
        endforeach;

        // Déclaration des événements
        $this->appAddAction('init');
    }

    /**
     * Initialisation globale
     *
     * @return void
     */
    public function init()
    {
        // Déclaration des champs personnalisés
        do_action('tify_field_register');
    }

    /**
     * Déclaration d'un controleur d'affichage
     *
     * @param string $name Nom de qualification du controleur d'affichage
     * @param mixed $callable classe ou méthode ou fonction de rappel
     *
     * @return null|callable|\tiFy\Core\Layout\AbstractFactory
     */
    public static function register($name, $callable)
    {
        if (self::has($name)) :
            return null;
        elseif (is_callable($callable)) :
            self::tFyAppAddContainer("tify.field.{$name}", $callable);
        elseif (class_exists($callable)) :
            self::tFyAppAddContainer("tify.field.{$name}", $callable);
        else :
            return null;
        endif;

        $return = self::get($name);

        return $return;
    }

    /**
     * Vérification d'existance d'un controleur d'affichage
     *
     * @param string $name Nom de qualification du controleur d'affichage
     *
     * @return bool
     */
    public static function has($name)
    {
        return self::tFyAppHasContainer("tify.field.{$name}");
    }

    /**
     * Récupération d'un controleur d'affichage
     *
     * @param string $name Nom de qualification du controleur d'affichage
     *
     * @return mixed|\tiFy\Core\Layout\AbstractFactory
     */
    public static function get($name)
    {
        if (self::has($name)) :
            return self::tFyAppGetContainer("tify.field.{$name}");
        endif;

        return null;
    }

    /**
     * Affichage ou récupération du contenu d'un controleur natif
     *
     * @param string $name Nom de qualification du controleur d'affichage
     * @param array $args {
     *      Liste des attributs de configuration
     *
     *      @var array $attrs Attributs de configuration du champ
     *      @var bool $echo Activation de l'affichage du champ
     *
     * @return null|Button|Checkbox|DatetimeJs|File|Hidden|Label|Number|NumberJs|Password|Radio|Repeater|Select|SelectJs|Submit|Text|Textarea|ToggleSwitch
     */
    public static function __callStatic($name, $arguments)
    {
        if(!$callable = self::get($name)) :
            return null;
        endif;

        return call_user_func_array($callable, $arguments);
    }

    /**
     * Mise en file des scripts d'un controleur
     *
     * @param string $name Identifiant de qualification du controleur d'affichage
     * @param array $args Liste des attributs de configuration
     *
     * @return null|callable
     */
    public static function enqueue($name, $args = [])
    {
        if(!$callable = self::get($name)) :
            return null;
        endif;

        if (!is_object($callable) || !method_exists($callable, 'enqueue_scripts')) :
            return null;
        endif;

        return $callable->enqueue_scripts($args);
    }

    /**
     * @deprecated
     *
     * {@inheritdoc}
     *
     * @return null|callable
     */
    public static function enqueue_scripts($name, $args = [])
    {
        return self::enqueue($name, $args = []);
    }
}