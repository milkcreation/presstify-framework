<?php

namespace tiFy\Apps\Container;

use Illuminate\Support\Arr;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ReflectionContainer;
use tiFy\Apps\AppControllerInterface;
use tiFy\Apps\ServiceProvider\ProviderItem;

class ServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
     * Classe de rappel du controleur de l'interface associée.
     * @var AppControllerInterface
     */
    protected $app;

    /**
     * Liste des alias de services fournis.
     * @internal requis. Tous les alias de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [];

    /**
     * Liste des alias de services à instance unique.
     * @var array
     */
    protected $singletons = [];

    /**
     * Liste des alias de services à instance multiples.
     * @var array
     */
    protected $bindings = [];

    /**
     * Liste des éléments déclarés.
     * @var ServiceInterface[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $provides Liste des élements.
     * @param AppControllerInterface $app Classe de rappel du controleur de l'application.
     *
     * @return void
     */
    public function __construct(AppControllerInterface $app)
    {
        $this->app = $app;

        $this->parse();
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getContainer()
    {
        return parent::getContainer();
    }
    
    /**
     * {@inheritdoc}
     */
    public function parse()
    {
        $items = [];
        $items += $this->singletons;
        $items += $this->bindings;

        foreach($items as $alias => $concrete) :
            if (is_numeric($alias)) :
                $alias = $concrete;
            endif;

            array_push($this->provides, $alias);

            $singleton = in_array($alias, $this->singletons);

            $item = new Service($alias, compact('concrete', 'singleton'), $this->app);

            if (!$item->isSingleton()) :
                $this->app->appServiceAdd($item->getAlias(), $item->getConcrete());
            else :
                $this->app->appServiceAdd($item->getAlias(), $item->getConcrete(), true)->withArgument($this->app);
            endif;

            $this->items[] = $item;
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {

    }
}