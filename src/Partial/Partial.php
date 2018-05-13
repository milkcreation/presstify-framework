<?php

/**
 * @name Partial
 * @desc Gestion de controleurs d'affichage
 * @package presstiFy
 * @namespace \tiFy\Partial
 * @version 1.1
 * @subpackage Core
 * @since 1.2.596
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Partial;

use Illuminate\Support\Arr;
use tiFy\Apps\AppController;
use tiFy\Components\Partials\Breadcrumb\Breadcrumb;
use tiFy\Components\Partials\Notice\Notice;
use tiFy\Components\Partials\Sidebar\Sidebar;
use tiFy\Components\Partials\Table\Table;
use tiFy\Components\Partials\Tag\Tag;

/**
 * @method static Breadcrumb Breadcrumb(string $id = null, array $attrs = [])
 * @method static Notice Notice(string $id = null,array $attrs = [])
 * @method static Sidebar Sidebar(string $id = null,array $attrs = [])
 * @method static Table Table(string $id = null,array $attrs = [])
 * @method static Tag Tag(string $id = null,array $attrs = [])
 */
final class Partial extends AppController
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
        foreach(glob($this->appAbsDir() . '/Components/Partials/*/', GLOB_ONLYDIR) as $filename) :
            $name = basename($filename);

            $this->register($name, "tiFy\\Components\\Partials\\{$name}\\{$name}::make");
        endforeach;

        do_action('tify_partial_register', $this);
    }

    /**
     * Déclaration d'un controleur d'affichage.
     *
     * @param string $name Nom de qualification du controleur d'affichage.
     * @param mixed $callable classe ou méthode ou fonction de rappel.
     *
     * @return null|callable|AbstractPartialController
     */
    public function register($name, $callable)
    {
        if ($this->appServiceHas($name)) :
            return null;
        endif;

        $alias = "tfy.partial.{$name}";

        if (is_callable($callable)) :
            $this->appServiceAdd($alias, $callable);
        elseif (class_exists($callable)) :
            $this->appServiceAdd($alias, $callable);
        else :
            return null;
        endif;

        return $this->appServiceGet($alias);
    }

    /**
     * Récupération d'un controleur d'affichage.
     *
     * @param string $name Nom de qualification du controleur d'affichage.
     *
     * @return mixed|AbstractPartialController
     */
    public function get($name)
    {
        $alias = "tfy.partial.{$name}";
        if ($this->appServiceHas($alias)) :
            return $this->appServiceGet($alias);
        endif;

        return null;
    }

    /**
     * Affichage ou récupération du contenu d'un controleur natif.
     *
     * @param string $name Nom de qualification du controleur d'affichage.
     * @param array $args {
     *      Liste des attributs de configuration.
     *
     *      @var array $attrs Attributs de configuration du champ.
     *      @var bool $echo Activation de l'affichage du champ.
     *
     * @return null|callable
     */
    public static function __callStatic($name, $args)
    {
        if(! $callable = self::appInstance()->get($name)) :
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
     * Mise en file des scripts d'un controleur.
     *
     * @param string $name Nom de qualification du controleur.
     * @param array $args Liste des variables passées en argument.
     *
     * @return $this
     */
    public function enqueue($name, $args = [])
    {
        if(!$callable = $this->get($name)) :
            return null;
        endif;

        if (! is_object($callable) || ! method_exists($callable, 'enqueue_scripts')) :
            return null;
        endif;

        call_user_func_array([$callable, 'enqueue_scripts'], $args);

        return $this;
    }
}