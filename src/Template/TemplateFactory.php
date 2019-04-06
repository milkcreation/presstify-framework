<?php declare(strict_types=1);

namespace tiFy\Template;

use tiFy\Contracts\Container\Container;
use tiFy\Contracts\Container\ServiceProvider;
use tiFy\Contracts\Template\FactoryAssets;
use tiFy\Contracts\Template\FactoryNotices;
use tiFy\Contracts\Template\FactoryRequest;
use tiFy\Contracts\Template\FactoryUrl;
use tiFy\Contracts\Template\TemplateFactory as TemplateFactoryContract;
use tiFy\Support\ParamsBag;
use tiFy\Template\Factory\FactoryServiceProvider;
use tiFy\Support\Str;
use tiFy\tiFy;

class TemplateFactory implements TemplateFactoryContract
{
    /**
     * Indicateur de démarrage du controleur.
     * @var boolean
     */
    private $booted = false;

    /**
     * Instance du controleur de traitement des attributs de configuration.
     * @var ParamsBag
     */
    protected $config;

    /**
     * Indicateur de chargement des ressources.
     * @var boolean
     */
    protected $loaded = false;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Identifiant de qualification compatible au formatage dans une url.
     * @var string
     */
    protected $slug;

    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [
        FactoryServiceProvider::class
    ];

    /**
     * CONSTRUCTEUR
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return void
     */
    public function __construct(?array $attrs = [])
    {
        $this->config = (new ParamsBag())->set($attrs);
    }

    /**
     * @inheritdoc
     */
    public function __invoke(string $name): TemplateFactoryContract
    {
        if (!$this->booted) {
            $this->name = $name;
            foreach ($this->getServiceProviders() as $serviceProvider) {
                $this->getContainer()->share(
                    "template.factory.service-provider.{$this->name}",
                    $resolved = new $serviceProvider($this)
                );

                if ($resolved instanceof ServiceProvider) {
                    $resolved->setContainer($this->getContainer());
                    $this->getContainer()->addServiceProvider($resolved);
                }
            }

            events()->trigger('template.factory.boot.' . $this->name, [&$this]);

            $this->boot();

            $this->booted = true;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        $this->load();

        return (string)$this->render();
    }

    /**
     * @inheritdoc
     */
    public function assets(): FactoryAssets
    {
        return $this->resolve('assets');
    }

    /**
     * @inheritdoc
     */
    public function boot()
    {

    }

    /**
     * @inheritdoc
     */
    public function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->config;
        } elseif(is_array($key)) {
            return $this->config->set($key);
        } else {
            return $this->config->get($key, $default);
        }
    }

    /**
     * @inheritdoc
     */
    public function db()
    {
        return $this->resolve('db');
    }

    /**
     * @inheritdoc
     */
    public function display()
    {
        echo (string)$this->render();
    }

    /**
     * @inheritdoc
     */
    public function getContainer(): Container
    {
        return tiFy::instance();
    }

    /**
     * @inheritdoc
     */
    public function getServiceProviders()
    {
        return $this->serviceProviders;
    }

    /**
     * @inheritdoc
     */
    public function bound(string $abstract)
    {
        return $this->getContainer()->has("template.factory.{$this->name()}.{$abstract}");
    }

    /**
     * @inheritdoc
     */
    public function label(?string $key = null, string $default = '')
    {
        $labels = $this->resolve('labels');

        return is_null($key) ? $labels : $labels->get($key, $default);
    }

    /**
     * @inheritdoc
     */
    public function load()
    {
        if (!$this->loaded) {
            $this->loaded = true;
            $this->process();
            $this->prepare();
        }
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function notices(): FactoryNotices
    {
        return $this->resolve('notices');
    }

    /**
     * @inheritdoc
     */
    public function param($key = null, $default = null)
    {
        $params = $this->resolve('params');

        if (is_null($key)) {
            return $params;
        } elseif (is_array($key)) {
            return $params->set($key);
        } else {
            return $params->get($key, $default);
        }
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {

    }

    /**
     * @inheritdoc
     */
    public function process()
    {

    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        return ($this->config()->has('content'))
            ? call_user_func_array($this->config('content'), [&$this])
            : __('Aucun contenu à afficher', 'tify');
    }

    /**
     * @inheritdoc
     */
    public function request(): FactoryRequest
    {
        return $this->resolve('request');
    }

    /**
     * @inheritdoc
     */
    public function resolve(string $abstract, array $args = [])
    {
        return $this->getContainer()->get("template.factory.{$this->name()}.{$abstract}", $args);
    }

    /**
     * @inheritdoc
     */
    public function slug(): string
    {
        if (is_null($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
        return $this->slug;
    }

    /**
     * @inheritdoc
     */
    public function url(): FactoryUrl
    {
        return $this->resolve('url');
    }

    /**
     * @inheritdoc
     */
    public function viewer(?string $view = null, array $data = [])
    {
        $viewer = $this->resolve('viewer');

        if (func_num_args() === 0) {
            return $viewer;
        }

        return $viewer->make("_override::{$view}", $data);
    }
}