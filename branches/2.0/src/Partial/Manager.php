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
use tiFy\Contracts\Partial\PartialController;
use tiFy\Contracts\Partial\Manager as ManagerInterface;
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
use tiFy\Partial\Spinner\Spinner;
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
final class Manager implements ManagerInterface
{
    /**
     * Liste des instances des éléments déclarés.
     * @var array
     */
    protected static $indexes = [];

    /**
     * Liste des alias de qualification des éléments.
     * @var array
     */
    protected $items = [
        'breadcrumb'    => Breadcrumb::class,
        'cookie-notice' => CookieNotice::class,
        'holder-image'  => HolderImage::class,
        'modal'         => Modal::class,
        'navtabs'       => Navtabs::class,
        'notice'        => Notice::class,
        'pagination'    => Pagination::class,
        'sidebar'       => Sidebar::class,
        'slider'        => Slider::class,
        'spinner'       => Spinner::class,
        'table'         => Table::class,
        'tag'           => Tag::class
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'after_setup_theme',
            function () {
                foreach ($this->items as $alias => $concrete) :
                    app()->bind("partial.{$alias}", $concrete)->build([null, []]);
                endforeach;
            },
            999999
        );
    }

    /**
     * Récupération statique d'un élément.
     *
     * @param string $name Nom de qualification.
     * @param array $args Liste des variables passées en arguments.
     *
     * @return null|PartialController
     */
    public static function __callStatic($name, $args)
    {
        array_unshift($args, $name);

        return call_user_func_array([app('partial'), 'get'], $args);
    }

    /**
     * Récupération de l'instance d'un élément déclaré.
     *
     * @param string $name Nom de qualification de l'élément.
     * @param mixed $id Nom de qualification ou Liste des attributs de configuration.
     * @param mixed $attrs Liste des attributs de configuration.
     *
     * @return null|PartialController
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
     * Récupération de l'index d'un contrôleur d'affichage déclaré.
     *
     * @param PartialController $partial Instance du contrôleur de champ.
     *
     * @return int
     */
    public function index(PartialController $partial)
    {
        $concrete = class_info($partial)->getName();
        $alias = array_search($concrete, $this->items);

        if ($alias === false) :
            return 0;
        endif;

        $count = empty(self::$indexes[$alias]) ? 0 : count(self::$indexes[$alias]);

        self::$indexes[$alias][$partial->getId()] = $partial;

        return $count;
    }

    /**
     * Déclaration d'un contrôleur d'affichage.
     *
     * @param string $name Nom de qualification d"appel de l'élément.
     * @param string $concrete Nom de qualification du controleur.
     *
     * @return $this
     */
    public function register($name, $concrete)
    {
        if (in_array($concrete, $this->items) || isset($this->items["partial.{$name}"])) :
            return false;
        endif;

        $this->items[$name] = $concrete;

        return true;
    }
}