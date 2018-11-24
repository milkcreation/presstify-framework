<?php

namespace tiFy\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use tiFy\Contracts\Routing\Router;
use tiFy\Contracts\Routing\RouteHandler as RouteHandlerContract;
use tiFy\Contracts\View\ViewController;
use tiFy\Kernel\Params\ParamsBag;

class RouteHandler extends ParamsBag implements RouteHandlerContract
{
    /**
     * Nom de qualification de la route.
     * @var string
     */
    protected $name = '';

    /**
     * Instance du contrôleur de routage.
     * @var Router
     */
    protected $router;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Identifiant de qualification de la route.
     * @param array $attrs Liste des attributs de configuration de la route.
     * @param Router $router
     *
     * @return void
     */
    public function __construct($name, $attrs = [], Router $router)
    {
        $this->name = $name;
        $this->router = $router;

        parent::__construct($attrs);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->router->setCurrent($this->name, $args);

        $callback = $this->get('cb');
        if (is_callable($callback)) :
            array_push($args, $request, $response);
            $resolved = call_user_func_array($callback, $args);

            if ($resolved instanceof ViewController) :
                $response->getBody()->write($resolved->render());
            else :
                $response->getBody()->write((string)$resolved);
            endif;
        endif;

        return $response;
    }
}