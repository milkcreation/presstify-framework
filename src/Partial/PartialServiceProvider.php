<?php

namespace tiFy\Partial;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Contracts\Partial\PartialItemInterface;
use tiFy\Partial\Partial;
use tiFy\Partial\Breadcrumb\Breadcrumb;
use tiFy\Partial\CookieNotice\CookieNotice;
use tiFy\Partial\HolderImage\HolderImage;
use tiFy\Partial\Modal\Modal;
use tiFy\Partial\Navtabs\Navtabs;
use tiFy\Partial\Notice\Notice;
use tiFy\Partial\Sidebar\Sidebar;
use tiFy\Partial\Slider\Slider;
use tiFy\Partial\Spinner\Spinner;
use tiFy\Partial\Table\Table;
use tiFy\Partial\Tag\Tag;

class PartialServiceProvider extends AppServiceProvider
{
    /**
     * Liste des instances des éléments déclarés.
     * @var array
     */
    protected static $instances = [];

    /**
     * Liste des alias de qualification des éléments.
     * @var array
     */
    protected $aliases = [
        'partial.breadcrumb'    => Breadcrumb::class,
        'partial.cookie-notice' => CookieNotice::class,
        'partial.holder-image'  => HolderImage::class,
        'partial.modal'         => Modal::class,
        'partial.navtabs'       => Navtabs::class,
        'partial.notice'        => Notice::class,
        'partial.sidebar'       => Sidebar::class,
        'partial.slider'        => Slider::class,
        'partial.spinner'       => Spinner::class,
        'partial.table'         => Table::class,
        'partial.tag'           => Tag::class
    ];

    /**
     * Liste des éléments à déclarer.
     * @var array
     */
    protected $items = [
        Breadcrumb::class,
        CookieNotice::class,
        HolderImage::class,
        Modal::class,
        Navtabs::class,
        Notice::class,
        Sidebar::class,
        Slider::class,
        Spinner::class,
        Table::class,
        Tag::class
    ];

    /**
     * Liste des services à instance unique auto-déclarés.
     * @var string[]
     */
    protected $singletons = [
        Partial::class
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton(
            Partial::class,
            function() {
                return new Partial();
            });

        add_action(
            'after_setup_theme',
            function() {
                foreach($this->aliases as $alias => $concrete) :
                    $this->getContainer()->setAlias($alias, $concrete);
                endforeach;

                foreach ($this->items as $concrete) :
                    $alias = $this->getContainer()->getAlias($concrete);

                    $this->app
                        ->bind(
                            $alias,
                            $concrete
                        )
                        ->build([null, []]);
                endforeach;
            }
        );
    }

    /**
     * Définition de l'instance d'un élément.
     *
     * @param PartialItemInterface $instance Instance de l'élément.
     *
     * @return int
     */
    public function setInstance($instance)
    {
        if (!$instance instanceof PartialItemInterface) :
            return 0;
        endif;

        $concrete = class_info($instance)->getName();
        $alias = $this->getContainer()->getAlias($concrete);

        $count = empty(self::$instances[$alias]) ? 0 : count(self::$instances[$alias]);

        self::$instances[$alias][] = $instance;

        return $count;
    }

    /**
     * Déclaration d'un controleur d'affichage.
     *
     * @param string $name Nom de qualification d"appel de l'élément.
     * @param string $concrete Nom de qualification du controleur.
     *
     * @return boolean
     */
    public function registerPartial($name, $concrete)
    {
        if (in_array($concrete, $this->items) || isset($this->aliases["partial.{$name}"])) :
            return false;
        endif;

        array_push($this->items, $concrete);
        $this->aliases["partial.{$name}"] = $concrete;

        return true;
    }
}