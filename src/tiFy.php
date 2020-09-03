<?php declare(strict_types=1);

namespace tiFy;

use App\App;
use tiFy\Contracts\Container\Container as ContainerContract;
use tiFy\Container\Container;
use tiFy\Kernel\Application;
use tiFy\Kernel\KernelServiceProvider;

/**
 * @desc PresstiFy -- Framework Milkcreation.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package tiFy
 * @version 2.0.334
 * @copyright Milkcreation
 */
final class tiFy extends Container
{
    /**
     * Instance de la classe
     * @var self
     */
    protected static $instance;

    /**
     * Instance de l'application.
     * @var App|Application|null
     */
    protected $app;

    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [
        KernelServiceProvider::class
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        if (self::instance()) {
            return;
        } else {
            self::$instance = $this;

            parent::__construct();
        }
    }

    /**
     * @inheritDoc
     */
    public function boot(): ContainerContract
    {
        /**  @deprecaded */
        if (defined('WP_INSTALLING') && (WP_INSTALLING === true)) {
            return $this;
        }
        /**/

        parent::boot();

        if (is_null($this->app)) {
            $this->app = class_exists(App::class) ? (new App($this)): (new Application($this));

            $this->share('app', $this->app->boot());
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get($alias, array $args = [])
    {
        return ($alias === 'app') ? $this->app : parent::get($alias, $args);
    }

    /**
     * Récupération de l'instance courante.
     *
     * @return static|null
     */
    public static function instance(): ?tiFy
    {
        return self::$instance instanceof static ? self::$instance : null;
    }
}
