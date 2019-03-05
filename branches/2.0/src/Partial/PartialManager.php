<?php

/**
 * @name PartialManager
 * @desc Gestion des controleurs d'affichage.
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Partial;

use Illuminate\Support\Str;
use tiFy\Contracts\Partial\PartialController;
use tiFy\Contracts\Partial\PartialManager as PartialManagerContract;
use tiFy\Partial\Partials\Accordion\Accordion;
use tiFy\Partial\Partials\Breadcrumb\Breadcrumb;
use tiFy\Partial\Partials\CookieNotice\CookieNotice;
use tiFy\Partial\Partials\Dropdown\Dropdown;
use tiFy\Partial\Partials\Holder\Holder;
use tiFy\Partial\Partials\Modal\Modal;
use tiFy\Partial\Partials\Navtabs\Navtabs;
use tiFy\Partial\Partials\Notice\Notice;
use tiFy\Partial\Partials\Pagination\Pagination;
use tiFy\Partial\Partials\Sidebar\Sidebar;
use tiFy\Partial\Partials\Slider\Slider;
use tiFy\Partial\Partials\Spinner\Spinner;
use tiFy\Partial\Partials\Table\Table;
use tiFy\Partial\Partials\Tag\Tag;

/**
 * @method static Accordion Accordion(string $id = null, array $attrs = [])
 * @method static Breadcrumb Breadcrumb(string $id = null, array $attrs = [])
 * @method static CookieNotice CookieNotice(string $id = null, array $attrs = [])
 * @method static Dropdown Dropdown(string $id = null,array $attrs = [])
 * @method static Holder Holder(string $id = null,array $attrs = [])
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
final class PartialManager implements PartialManagerContract
{
    /**
     * Liste des instances des éléments déclarés.
     * @var array
     */
    protected $instances = [];

    /**
     * Liste des alias de qualification des éléments.
     * @var array
     */
    protected $items = [
        'accordion'     => Accordion::class,
        'breadcrumb'    => Breadcrumb::class,
        'cookie-notice' => CookieNotice::class,
        'dropdown'      => Dropdown::class,
        'holder'        => Holder::class,
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
        add_action('after_setup_theme', function () {
            foreach ($this->items as $alias => $concrete) :
                app()->bind("partial.{$alias}", $concrete)->build([null, null]);
            endforeach;
        }, 999999);
    }

    /**
     * {@inheritdoc}
     */
    public static function __callStatic($name, $args)
    {
        array_unshift($args, $name);

        return call_user_func_array([partial(), 'get'], $args);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function index(PartialController $partial)
    {
        $concrete = class_info($partial)->getName();
        $alias = array_search($concrete, $this->items);

        if ($alias === false) :
            return 0;
        endif;

        $count = empty($this->instances[$alias]) ? 0 : count($this->instances[$alias]);

        $this->instances[$alias][$partial->getId()] = $partial;

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function register($name, $concrete)
    {
        if (in_array($concrete, $this->items) || isset($this->items["partial.{$name}"])) :
            return false;
        endif;

        $this->items[$name] = $concrete;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function resourcesDir($path = '')
    {
        $path = $path ? '/' . ltrim($path, '/') : '';

        return (file_exists(__DIR__ . "/Resources{$path}"))
            ? __DIR__ . "/Resources{$path}"
            : '';
    }

    /**
     * {@inheritdoc}
     */
    public function resourcesUrl($path = '')
    {
        $cinfo = class_info($this);
        $path = $path ? '/' . ltrim($path, '/') : '';

        return (file_exists($cinfo->getDirname() . "/Resources{$path}"))
            ? $cinfo->getUrl() . "/Resources{$path}"
            : '';
    }
}