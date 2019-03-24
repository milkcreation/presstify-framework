<?php declare(strict_types=1);

namespace tiFy\Partial;

use BadMethodCallException;
use Exception;
use tiFy\Support\Str;
use tiFy\Contracts\Partial\PartialFactory;
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
 * Class PartialManager
 * @package tiFy\Partial
 *
 * @method static Accordion Accordion(string $id = null, array $attrs = [])
 * @method static Breadcrumb Breadcrumb(string $id = null, array $attrs = [])
 * @method static CookieNotice CookieNotice(string $id = null, array $attrs = [])
 * @method static Dropdown Dropdown(string $id = null, array $attrs = [])
 * @method static Holder Holder(string $id = null, array $attrs = [])
 * @method static Modal Modal(string $id = null, array $attrs = [])
 * @method static Navtabs Navtabs(string $id = null, array $attrs = [])
 * @method static Notice Notice(string $id = null, array $attrs = [])
 * @method static Pagination Pagination(string $id = null, array $attrs = [])
 * @method static Sidebar Sidebar(string $id = null, array $attrs = [])
 * @method static Slider Slider(string $id = null, array $attrs = [])
 * @method static Spinner Spinner(string $id = null, array $attrs = [])
 * @method static Table Table(string $id = null, array $attrs = [])
 * @method static Tag Tag(string $id = null, array $attrs = [])
 */
class PartialManager implements PartialManagerContract
{
    /**
     * Instance du gestionnaire de gabarit d'affichage.
     * @var PartialManagerContract
     */
    protected static $instance;

    /**
     * Liste des noms de qualification de classes des gabarits d'affichage.
     * @var array
     */
    protected $classnames = [];

    /**
     * Instances indexées des gabarits d'affichage.
     * @var array
     */
    protected $indexes = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        if (is_null(static::$instance)) {
            static::$instance = $this;
        }
    }

    /**
     * @inheritdoc
     */
    public static function __callStatic(string $name, ?array $arguments): ?PartialFactory
    {
        try {
            return static::$instance->get($name, ...$arguments);
        } catch (Exception $e) {
            throw new BadMethodCallException(
                sprintf(__('La gabarit d\'affichage %s n\'est pas disponible.', 'tify'), $name)
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function classname(PartialFactory $factory): ?string
    {
        return class_info($factory)->getName();
    }

    /**
     * @inheritdoc
     */
    public function get(string $alias, $id = null, ?array $attrs = null): ?PartialFactory
    {
        $alias = Str::kebab($alias);
        $abstract = "partial.factory.{$alias}";
        $classname = $this->classnames[$alias];

        if (is_array($id)) {
            $attrs = $id;
            $id = null;
        } else {
            $attrs = $attrs ?: [];
        }

        if (!is_null($id) && isset($this->indexes[$classname][$id])) {
            return $this->indexes[$classname][$id];
        }

        return app()->get($abstract, [$id, $attrs, $this]);
    }

    /**
     * @inheritdoc
     */
    public function index(PartialFactory $factory): int
    {
        $classname = $this->classname($factory);

        $index = !isset($this->indexes[$classname]) ? 0 : count($this->indexes[$classname]);
        $this->indexes[$classname][$factory->getId()] = $factory;

        return $index;
    }

    /**
     * @inheritdoc
     */
    public function register(string $alias, PartialFactory $factory): ?PartialManagerContract
    {
        if(!isset($this->classnames[$alias])){
            $this->classnames[$alias] = $this->classname($factory);
        }

        if (!app()->has("partial.factory.{$alias}")) {
            app()->add("partial.factory.{$alias}", function () use ($factory) {
                return $factory;
            });
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function resourcesDir(string $path = null): string
    {
        $path = $path ? '/' . ltrim($path, '/') : '';

        return (file_exists(__DIR__ . "/Resources{$path}"))
            ? __DIR__ . "/Resources{$path}"
            : '';
    }

    /**
     * @inheritdoc
     */
    public function resourcesUrl(string $path = null): string
    {
        $cinfo = class_info($this);
        $path = $path ? '/' . ltrim($path, '/') : '';

        return (file_exists($cinfo->getDirname() . "/Resources{$path}"))
            ? $cinfo->getUrl() . "/Resources{$path}"
            : '';
    }
}