<?php

/**
 * @name Field
 * @desc Gestion de controleurs d'affichage
 * @package presstiFy
 * @namespace \tiFy\Field
 * @version 1.1
 * @subpackage Core
 * @since 1.2.596
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Field;

use Illuminate\Support\Arr;
use tiFy\App\AppController;
use tiFy\Components\Field\Button\Button;
use tiFy\Components\Field\Checkbox\Checkbox;
use tiFy\Components\Field\CheckboxCollection\CheckboxCollection;
use tiFy\Components\Field\Colorpicker\Colorpicker;
use tiFy\Components\Field\Crypted\Crypted;
use tiFy\Components\Field\DatetimeJs\DatetimeJs;
use tiFy\Components\Field\File\File;
use tiFy\Components\Field\Hidden\Hidden;
use tiFy\Components\Field\Label\Label;
use tiFy\Components\Field\MediaFile\MediaFile;
use tiFy\Components\Field\MediaImage\MediaImage;
use tiFy\Components\Field\Number\Number;
use tiFy\Components\Field\NumberJs\NumberJs;
use tiFy\Components\Field\Password\Password;
use tiFy\Components\Field\Radio\Radio;
use tiFy\Components\Field\RadioCollection\RadioCollection;
use tiFy\Components\Field\Repeater\Repeater;
use tiFy\Components\Field\Select\Select;
use tiFy\Components\Field\SelectImage\SelectImage;
use tiFy\Components\Field\SelectJs\SelectJs;
use tiFy\Components\Field\Submit\Submit;
use tiFy\Components\Field\Text\Text;
use tiFy\Components\Field\Textarea\Textarea;
use tiFy\Components\Field\TextRemaining\TextRemaining;
use tiFy\Components\Field\ToggleSwitch\ToggleSwitch;

/**
 * Class Field
 * @package tiFy\Field
 *
 * @method static Button Button(string $id = null, array $attrs = [])
 * @method static Checkbox(string $id = null, array $attrs = [])
 * @method static CheckboxCollection(string $id = null, array $attrs = [])
 * @method static Colorpicker(string $id = null, array $attrs = [])
 * @method static Crypted(string $id = null, array $attrs = [])
 * @method static DatetimeJs(string $id = null, array $attrs = [])
 * @method static File(string $id = null, array $attrs = [])
 * @method static Hidden(string $id = null, array $attrs = [])
 * @method static Label(string $id = null, array $attrs = [])
 * @method static MediaFile(string $id = null, array $attrs = [])
 * @method static MediaImage(string $id = null, array $attrs = [])
 * @method static Number(string $id = null, array $attrs = [])
 * @method static NumberJs(string $id = null, array $attrs = [])
 * @method static Password(string $id = null, array $attrs = [])
 * @method static Radio(string $id = null, array $attrs = [])
 * @method static RadioCollection(string $id = null, array $attrs = [])
 * @method static Repeater(string $id = null, array $attrs = [])
 * @method static Select(string $id = null, array $attrs = [])
 * @method static SelectImage(string $id = null, array $attrs = [])
 * @method static SelectJs(string $id = null, array $attrs = [])
 * @method static Submit(string $id = null, array $attrs = [])
 * @method static Text(string $id = null, array $attrs = [])
 * @method static Textarea(string $id = null, array $attrs = [])
 * @method static TextRemaining(string $id = null, array $attrs = [])
 * @method static ToggleSwitch(string $id = null, array $attrs = [])
 */
final class Field extends AppController
{
    /**
     * Liste des instances de champ.
     * @var array
     */
    protected static $instance = [];

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        // Déclaration des controleurs d'affichage natifs
        foreach (glob($this->appAbsDir() . '/Components/Field/*/', GLOB_ONLYDIR) as $filename) :
            $name = basename($filename);

            $this->register($name, "tiFy\\Components\\Field\\{$name}\\{$name}::make");
        endforeach;

        do_action('tify_field_register');
    }

    /**
     * Déclaration d'un controleur d'affichage
     *
     * @param string $name Nom de qualification du controleur d'affichage
     * @param mixed $callable classe ou méthode ou fonction de rappel
     *
     * @return null|callable|\tiFy\Partial\AbstractFactory
     */
    public function register($name, $callable)
    {
        if ($this->appServiceHas($name)) :
            return null;
        endif;

        $alias = "tfy.field.{$name}";
        if (is_callable($callable)) :
            $this->appServiceAdd($alias, $callable);
        elseif (class_exists($callable)) :
            $this->appServiceAdd($alias, $callable);
        else :
            return null;
        endif;

        return $this->appServiceGet("tfy.field.{$name}");
    }

    /**
     * Récupération d'un controleur d'affichage.
     *
     * @param string $name Nom de qualification du controleur d'affichage.
     *
     * @return mixed|FieldItemInterface
     */
    public function get($name)
    {
        $alias = "tfy.field.{$name}";
        if ($this->appServiceHas($alias)) :
            return $this->appServiceGet($alias);
        endif;

        return null;
    }

    /**
     * Affichage ou récupération du contenu d'un controleur natif
     *
     * @param string $name Nom de qualification du controleur d'affichage
     * @param array args Liste des variables passé en arguments
     *
     * @return null|object
     */
    public static function __callStatic($name, $args)
    {
        if (!$callable = self::appInstance()->get($name)) :
            return null;
        endif;

        return call_user_func_array($callable, $args);
    }

    /**
     * Vérification d'existance d'une instance d'un contrôleur de champ.
     *
     * @param string $classname Nom de la classe de rappel du controleur.
     *
     * @return bool
     */
    public function existsInstance($classname)
    {
        return Arr::has(self::$instance, $classname);
    }

    /**
     * Compte le nombre d'instance d'un contrôleur de champ.
     *
     * @param string $classname Nom de la classe de rappel du controleur.
     *
     * @return int
     */
    public function countInstance($classname)
    {
        return count(Arr::get(self::$instance, $classname, []));
    }

    /**
     * Définition d'une instance de contrôleur de champ.
     *
     * @param string $classname Nom de la classe de rappel du controleur.
     * @param string $hash Hashage des attributs de configuration.
     *
     * @return bool
     */
    public function setInstance($classname, $hash, $obj)
    {
        return Arr::set(self::$instance, "{$classname}.{$hash}", $obj);
    }

    /**
     * Définition d'une instance de contrôleur de champ.
     *
     * @param string $classname Nom de la classe de rappel du controleur.
     * @param string $hash Hashage des attributs de configuration.
     *
     * @return bool
     */
    public function getInstance($classname, $hash)
    {
        return Arr::get(self::$instance, "{$classname}.{$hash}");
    }

    /**
     * Mise en file des scripts d'un controleur d'affichage.
     *
     * @param string $name Nom de qualification du controleur.
     * @param array $args Liste des variables passées en argument.
     *
     * @return $this
     */
    public function enqueue($name, $args = [])
    {
        if (!$callable = $this->get($name)) :
            return null;
        endif;

        if (!is_object($callable) || !method_exists($callable, 'enqueue_scripts')) :
            return null;
        endif;

        call_user_func_array([$callable, 'enqueue_scripts'], $args);

        return $this;
    }
}