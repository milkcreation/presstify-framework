<?php declare(strict_types=1);

namespace tiFy\Template;

use tiFy\Contracts\Template\{
    FactoryAssets,
    FactoryNotices,
    FactoryRequest,
    FactoryServiceProvider,
    FactoryUrl,
    TemplateFactory as TemplateFactoryContract,
    TemplateManager as TemplateManagerContract};
use tiFy\Support\ParamsBag;
use tiFy\Support\Str;

class TemplateFactory extends ParamsBag implements TemplateFactoryContract
{
    /**
     * Liste des instances de template déclaré.
     * @var TemplateFactoryContract[]
     */
    private static $instance = [];

    /**
     * Instance du gestionnaire de templates.
     * @var TemplateManagerContract
     */
    protected $manager;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Indicateur de préparation du template.
     * @var boolean
     */
    protected $prepared = false;

    /**
     * Identifiant de qualification compatible au formatage dans une url.
     * @var string|null
     */
    protected $slug;

    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [
        Factory\FactoryServiceProvider::class
    ];

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        $this->prepare();

        return (string)$this->render();
    }

    /**
     * @inheritDoc
     */
    public function assets(): FactoryAssets
    {
        return $this->resolve('assets');
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {

    }

    /**
     * @inheritDoc
     */
    public function db()
    {
        return $this->resolve('db');
    }

    /**
     * @inheritDoc
     */
    public function display()
    {
        echo (string)$this->render();
    }

    /**
     * @inheritDoc
     */
    public function getServiceProviders()
    {
        return $this->serviceProviders;
    }

    /**
     * @inheritDoc
     */
    public function bound(string $abstract)
    {
        return $this->manager->getContainer()->has("template.factory.{$this->name()}.{$abstract}");
    }

    /**
     * @inheritDoc
     */
    public function label(?string $key = null, string $default = '')
    {
        $labels = $this->resolve('labels');

        return is_null($key) ? $labels : $labels->get($key, $default);
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function notices(): FactoryNotices
    {
        return $this->resolve('notices');
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function prepare(): TemplateFactoryContract
    {
        if (!$this->prepared) {
            $this->process();
            $this->prepared = true;
            events()->trigger('template.factory.prepare', [$this->name(), &$this]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function process(): TemplateFactoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        return ($this->has('content'))
            ? call_user_func_array($this->get('content'), [&$this])
            : __('Aucun contenu à afficher', 'tify');
    }

    /**
     * @inheritDoc
     */
    public function request(): FactoryRequest
    {
        return $this->resolve('request');
    }

    /**
     * @inheritDoc
     */
    public function resolve(string $abstract, array $args = [])
    {
        return $this->manager->getContainer()->get("template.factory.{$this->name()}.{$abstract}", $args);
    }

    /**
     * @inheritDoc
     */
    public function setInstance(string $name, TemplateManagerContract $manager): TemplateFactoryContract
    {
        if (!isset(self::$instance[$name])) {
            self::$instance[$name] = $this;

            $this->name = $name;
            $this->manager = $manager;

            $this->boot();
            $this->parse();

            foreach ($this->getServiceProviders() as $serviceProvider) {
                $resolved = new $serviceProvider();

                if ($resolved instanceof FactoryServiceProvider) {
                    $resolved->setFactory($this)->setContainer($this->manager->getContainer());
                    $this->manager->getContainer()->addServiceProvider($resolved);
                }
            }
            events()->trigger('template.factory.boot', [$this->name(), &$this]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function slug(): string
    {
        if (is_null($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
        return $this->slug;
    }

    /**
     * @inheritDoc
     */
    public function url(): FactoryUrl
    {
        return $this->resolve('url');
    }

    /**
     * @inheritDoc
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