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
     * Classe de rappel du controleur de l'interface associée.
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
     * Cartographie des services fournis.
     * @internal Couple $key => $concrete.
     * @var array
     */
    protected $providers = [];

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
        if ($item->isInstanciated()) :
            return;
        endif;

        if (!$item->isSingleton()) :
            $resolve = $this->getContainer()->add($item->getAlias(), $item->getConcrete());
        else :
            $resolve = $this->getContainer()->add($item->getAlias(), $item->getConcrete(), true);
        endif;

        $args = $item->getArgs();
        array_push($args, $this->app);

        $resolve->withArguments($args);

        return $item->setInstanciated();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if ($this->delegate) :
            $this->getContainer()->delegate(new ReflectionContainer());
        endif;

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
    public function get($key, $args = null)
    {
        /** @var ProviderItem $item */
        if (!$item = Arr::get($this->items, $key)) :
            return;
        endif;

        if ($item->isDeferred() && !is_null($args)) :
            $item->setArgs($args);
        endif;

        if ($this->add($item)) :
            return $this->getContainer()->get($item->getAlias());
        else :
            $args = !is_null($args) ? $args : [];
            array_push($args, $this->app);

            return $this->getContainer()->get($item->getAlias(), $args);
        endif;
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
        foreach($items as $name => $attrs) :
            if (is_string($attrs)) :
                $attrs = [
                    'alias'     => $attrs,
                    'concrete'  => $attrs
                ];
            endif;

            $attrs['concrete'] = $this->parseConcrete($name, $attrs['concrete']);

            $item = new ProviderItem($name, $attrs, $this->app);
            array_push($this->provides, $item->getAlias());

            $this->items[$name] = $item;
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function parseConcrete($key, $default)
    {
        return Arr::get($this->providers, 'key', $default);
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {

    }
}