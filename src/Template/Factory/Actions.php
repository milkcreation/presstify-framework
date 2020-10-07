<?php declare(strict_types=1);

namespace tiFy\Template\Factory;

use Exception;
use tiFy\Contracts\Template\{
    FactoryActions as FactoryActionsContract,
    FactoryHttpController as FactoryHttpControllerContract,
    FactoryHttpXhrController as FactoryHttpXhrControllerContract
};
use tiFy\Support\Str;
use tiFy\Routing\BaseController;

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
        } else {
            throw new Exception(__('Impossible d\'exécuter l\'action.', 'tify'));
        }
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