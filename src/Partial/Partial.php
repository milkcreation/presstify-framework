<?php

/**
 * @name Partial.
 * @desc Gestion des controleurs d'affichage.
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Partial;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use tiFy\Contracts\Partial\PartialItemInterface;
use tiFy\Partial\Breadcrumb\Breadcrumb;
use tiFy\Partial\CookieNotice\CookieNotice;
use tiFy\Partial\HolderImage\HolderImage;
use tiFy\Partial\Modal\Modal;
use tiFy\Partial\Navtabs\Navtabs;
use tiFy\Partial\Notice\Notice;
use tiFy\Partial\Pagination\Pagination;
use tiFy\Partial\PartialServiceProvider;
use tiFy\Partial\Sidebar\Sidebar;
use tiFy\Partial\Slider\Slider;
use tiFy\Partial\Table\Table;
use tiFy\Partial\Tag\Tag;

/**
 * @method static Breadcrumb Breadcrumb(string $id = null, array $attrs = [])
 * @method static CookieNotice CookieNotice(string $id = null, array $attrs = [])
 * @method static HolderImage HolderImage(string $id = null,array $attrs = [])
 * @method static Modal Modal(string $id = null,array $attrs = [])
 * @method static Navtabs Navtabs(string $id = null,array $attrs = [])
 * @method static Notice Notice(string $id = null,array $attrs = [])
 * @method static Pagination Pagination(string $id = null,array $attrs = [])
 * @method static Sidebar Sidebar(string $id = null,array $attrs = [])
 * @method static Slider Slider(string $id = null,array $attrs = [])
 * @method static Spinner Spinner(string $id = null,array $attrs = [])
 * @method static Table Table(string $id = null,array $attrs = [])
 * @method static Tag Tag(string $id = null,array $attrs = [])
 */
final class Partial
{
    /**
     * Récupération statique d'un élément.
     *
     * @param string $name Nom de qualification.
     * @param array $args Liste des variables passées en arguments.
     *
     * @return null|callable
     */
    public static function __callStatic($name, $args)
    {
        array_unshift($args, $name);

        return call_user_func_array([app(Partial::class), 'get'], $args);
    }

    /**
     * Récupération de l'instance d'un élément déclaré.
     *
     * @param string $name Nom de qualification de l'élément.
     * @param mixed $id Nom de qualification ou Liste des attributs de configuration.
     * @param mixed $attrs Liste des attributs de configuration.
     *
     * @return PartialItemInterface
     */
    public function get($name, $id = null, $attrs = null)
    {
        $alias = 'partial.' . Str::kebab($name);

        if (is_array($id)) :
            $attrs = $id;
            $id = null;
        else :
            $attrs = $attrs ? : [];
        endif;

        return app()->resolve($alias, [$id, $attrs]);
    }

    /**
     * Déclaration d'un controleur d'affichage.
     *
     * @param string $name Nom de qualification d"appel de l'élément.
     * @param string $concrete Nom de qualification du controleur.
     *
     * @return $this
     */
    public function register($name, $concrete)
    {
        app(PartialServiceProvider::class)->registerPartial($name, $concrete);

        return $this;
    }
}