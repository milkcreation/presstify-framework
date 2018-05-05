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

use tiFy\Apps\AppController;
use tiFy\Components;

/**
 * @method static \tiFy\Components\Partials\Breadcrumb\Breadcrumb Breadcrumb(string $id = null, array $attrs = [])
 * @method static \tiFy\Components\Partials\Notice\Notice Notice(string $id = null,array $attrs = [])
 * @method static \tiFy\Components\Partials\Tag\Tag Tag(string $id = null,array $attrs = [])
 */
final class Partial extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
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
     * @return null|callable|\tiFy\Partial\AbstractFactory
     */
    public function register($name, $callable)
    {
        if ($this->has($name)) :
            return null;
        endif;

        $alias = "tify.partial.{$name}";
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
     * Vérification d'existance d'un controleur d'affichage.
     *
     * @param string $name Nom de qualification du controleur d'affichage.
     *
     * @return bool
     */
    public function has($name)
    {
        return $this->appServiceHas("tify.partial.{$name}");
    }

    /**
     * Récupération d'un controleur d'affichage.
     *
     * @param string $name Nom de qualification du controleur d'affichage.
     *
     * @return mixed|\tiFy\Partial\AbstractFactory
     */
    public function get($name)
    {
        if ($this->has($name)) :
            return $this->appServiceGet("tify.partial.{$name}");
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
    public static function __callStatic($name, $arguments)
    {
        $instance = self::appInstance();

        if(! $callable = $instance->get($name)) :
            return null;
        endif;

        return call_user_func_array($callable, $arguments);
    }

    /**
     * Mise en file des scripts d'un controleur.
     *
     * @param string $name Identifiant de qualification du controleur d'affichage.
     * @param array $args Liste des attributs de configuration.
     *
     * @return null|callable
     */
    public function enqueue($name, $args = [])
    {
        if(!$callable = $this->get($name)) :
            return null;
        endif;

        if (! is_object($callable) || ! method_exists($callable, 'enqueue_scripts')) :
            return null;
        endif;

        return $callable->enqueue_scripts($args);
    }
}