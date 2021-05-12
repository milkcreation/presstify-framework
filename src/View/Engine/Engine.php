<?php declare(strict_types=1);

namespace tiFy\View\Engine;

use tiFy\Contracts\View\Engine as EngineContract;
use tiFy\Contracts\View\View as ViewContract;
use tiFy\Support\ParamsBag;

abstract class Engine implements EngineContract
{
    /**
     * Instance du gestionnaire de paramètres.
     * @var ParamsBag
     */
    protected $params;

    /**
     * Instance du gestionnaire de vue.
     * @var ViewContract|null
     */
    protected $manager;

    /**
     * @inheritDoc
     */
    abstract public function addPath(string $path, ?string $name = null): EngineContract;

    /**
     * @inheritDoc
     */
    public function exists($name)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function params($key = null, $default = null)
    {
        if (is_null($this->params)) {
            $this->params = new ParamsBag();
        }

        if (is_string($key)) {
            return $this->params->get($key, $default);
        }

        if (is_array($key)) {
            return $this->params->set($key);
        }

        return $this->params;
    }

    /**
     * @inheritDoc
     */
    abstract public function make($name);

    /**
     * @inheritDoc
     */
    public function manager(): ViewContract
    {
        return $this->manager;
    }

    /**
     * @inheritDoc
     */
    abstract public function render($name, array $args = []);

    /**
     * @inheritDoc
     */
    public function share($key, $value = null): EngineContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setManager(ViewContract $manager): EngineContract
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setParams(array $params): EngineContract
    {
        $this->params($params);

        return $this;
    }
}