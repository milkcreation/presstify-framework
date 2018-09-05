<?php

namespace tiFy\Partial;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use tiFy\App\Container\AppServiceProvider;
use tiFy\Contracts\Partial\PartialItemInterface;
use tiFy\Partial\Partial;
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
        'breadcrumb'    => Breadcrumb::class,
        'cookie-notice' => CookieNotice::class,
        'holder-image'  => HolderImage::class,
        'modal'         => Modal::class,
        'modal-trigger' => ModalTrigger::class,
        'navtabs'       => Navtabs::class,
        'notice'        => Notice::class,
        'sidebar'       => Sidebar::class,
        'slider'        => Slider::class,
        'table'         => Table::class,
        'tag'           => Tag::class
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
        ModalTrigger::class,
        Navtabs::class,
        Notice::class,
        Sidebar::class,
        Slider::class,
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
        foreach($this->aliases as $alias => $concrete) :
            $this->getContainer()->setAlias($alias, $concrete);
        endforeach;

        $this->app->resolve(Partial::class, [$this->app]);

        $this->app->appAddAction(
            'after_setup_theme',
            function() {
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
     * Récupération de l'instance d'un élément.
     *
     * @param string $name Nom de qualification de l'élément.
     * @param array $args Liste des variables passées en argument au moment de l'instanciation.
     *
     * @return PartialItemInterface
     */
    public function get($name, $args = [])
    {
        $alias = Str::kebab($name);

        return $this->app->resolve($alias, [$args]);
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
}