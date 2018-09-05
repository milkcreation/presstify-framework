<?php

/**
 * @name Partial
 * @desc Gestion des controleurs d'affichage.
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Partial;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use tiFy\App\Dependency\AbstractAppDependency;
use tiFy\Partial\Breadcrumb\Breadcrumb;
use tiFy\Partial\CookieNotice\CookieNotice;
use tiFy\Partial\HolderImage\HolderImage;
use tiFy\Partial\Modal\Modal;
use tiFy\Partial\ModalTrigger\ModalTrigger;
use tiFy\Partial\Navtabs\Navtabs;
use tiFy\Partial\Notice\Notice;
use tiFy\Partial\Sidebar\Sidebar;
use tiFy\Partial\Slider\Slider;
use tiFy\Partial\Table\Table;
use tiFy\Partial\Tag\Tag;

/**
 * @method static Breadcrumb Breadcrumb(string $id = null, array $attrs = [])
 * @method static CookieNotice CookieNotice(string $id = null, array $attrs = [])
 * @method static HolderImage HolderImage(string $id = null,array $attrs = [])
 * @method static Modal Modal(string $id = null,array $attrs = [])
 * @method static ModalTrigger ModalTrigger(string $id = null,array $attrs = [])
 * @method static Navtabs Navtabs(string $id = null,array $attrs = [])
 * @method static Notice Notice(string $id = null,array $attrs = [])
 * @method static Sidebar Sidebar(string $id = null,array $attrs = [])
 * @method static Slider Slider(string $id = null,array $attrs = [])
 * @method static Spinner Spinner(string $id = null,array $attrs = [])
 * @method static Table Table(string $id = null,array $attrs = [])
 * @method static Tag Tag(string $id = null,array $attrs = [])
 */
class Partial extends AbstractAppDependency
{
    /**
     * Récupération statique d'un controleur d'affichage.
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
    public static function __callStatic($name, $attrs)
    {
        /** @var PartialServiceProvider $serviceProvider */
        $serviceProvider = app(PartialServiceProvider::class);

        array_unshift($attrs, $name);

        return call_user_func_array([$serviceProvider, 'get'], $attrs);
    }

    /**
     *
     */
    public function get($name, $attrs = [])
    {
        /** @var PartialServiceProvider $serviceProvider */
        $serviceProvider = $this->app(PartialServiceProvider::class);

        return $serviceProvider->get($name, $attrs);
    }
}