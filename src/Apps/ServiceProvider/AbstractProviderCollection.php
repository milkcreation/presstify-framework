<?php

namespace tiFy\Apps\ServiceProvider;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use League\Container\Container;
use League\Container\ContainerInterface;
use League\Container\Exception\NotFoundException;
use League\Container\ServiceProvider\AbstractServiceProvider as LeagueAbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use League\Container\ReflectionContainer;
use LogicException;
use ReflectionFunction;
use ReflectionException;
use tiFy\Apps\AppControllerInterface;
use tiFy\Apps\ServiceProvider\ProviderItem;

abstract class AbstractProviderCollection extends LeagueAbstractServiceProvider implements ProviderCollectionInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'administration associée.
     * @var AppControllerInterface
     */
    protected $app;

    /**
     * Activation de l'auto-wiring.
     * @var bool
     */
    protected $delegate = false;

    /**
     * Liste des alias de services fournis.
     * @internal requis. Tous les alias de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [];

    /**
     * Liste des éléments.
     * @var array
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
    public function __construct($items, AppControllerInterface $app)
    {
        $this->app = $app;

        $items = array_merge(
            $this->defaults(),
            $items
        );

        $this->parse($items);
    }

    /**
     * {@inheritdoc}
     */
    public function add($item)
    {
        if ($item->isSingleton()) :
            $resolve = $this->getContainer()->share($item->getAlias(), $item->getConcrete());
        else :
            $resolve = $this->getContainer()->add($item->getAlias(), $item->getConcrete());
        endif;

        $args = $item->getArgs();

        array_push($args, $this->app);

        $resolve->withArguments($args);
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if ($this->delegate) :
            $this->getContainer()->delegate(new ReflectionContainer());
        endif;

        foreach ($this->getBootable() as $item) :
            $this->add($item);
        endforeach;

        foreach ($this->getBootable() as $key => $item) :
            $this->get($key);
        endforeach;
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
    public function get($key, $args = [])
    {
        if (!$item =  Arr::get($this->items, $key)) :
            return;
        endif;

        array_push($args, $this->app);

        return $this->getContainer()->get($item->getAlias(), $args);
    }

    /**
     * {@inheritdoc}
     */
    public function getBootable()
    {
        return (new Collection($this->items))->where('bootable', '===', true)->all();
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
    public function getDeferred()
    {
        return (new Collection($this->items))->where('bootable', '===', false)->all();
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return Arr::has($this->items, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($items)
    {
        foreach($items as $key => $attrs) :
            if (is_string($attrs)) :
                $attrs = [
                    'alias'     => $attrs,
                    'concrete'  => $attrs
                ];
            endif;

            $item = new ProviderItem($attrs, $this->app);
            array_push($this->provides, $item->getAlias());

            $this->items[$key] = $item;
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        foreach ($this->getDeferred() as $item) :
            $this->add($item);
        endforeach;
    }
}