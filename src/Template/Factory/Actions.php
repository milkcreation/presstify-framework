<?php

declare(strict_types=1);

namespace tiFy\Template\Factory;

use Exception;
use Pollen\Routing\BaseController;
use tiFy\Contracts\Template\FactoryActions as FactoryActionsContract;
use tiFy\Contracts\Template\FactoryHttpController as FactoryHttpControllerContract;
use tiFy\Contracts\Template\FactoryHttpXhrController as FactoryHttpXhrControllerContract;
use tiFy\Support\Str;

class Actions implements FactoryActionsContract
{
    use FactoryAwareTrait;

    /**
     * Instance du controleur de requête HTTP.
     * @var BaseController|FactoryHttpControllerContract|FactoryHttpXhrControllerContract|null
     */
    protected $controller;

    /**
     * @inheritDoc
     */
    public function controller(): BaseController
    {
        if (is_null($this->controller)) {
            $this->controller = new BaseController();
        }

        return $this->controller;
    }

    /**
     * @inheritDoc
     */
    public function do(string $name, ...$parameters)
    {
        $method = 'do' . Str::studly($name);

        if (method_exists($this, $method)) {
            return $this->$method(...$parameters);
        }
        throw new Exception(__('Impossible d\'exécuter l\'action.', 'tify'));
    }

    /**
     * @inheritDoc
     */
    public function getIndex(): string
    {
        return 'action';
    }

    /**
     * @inheritDoc
     */
    public function setController(BaseController $controller): FactoryActionsContract
    {
        $this->controller = $controller;

        return $this;
    }
}