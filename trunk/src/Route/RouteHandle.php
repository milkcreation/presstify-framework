<?php

namespace tiFy\Route;

use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use tiFy\Apps\AppController;
use tiFy\Route\Route;

class RouteHandle extends AppController
{
    /**
     * Nom de qualification de la route.
     * @var string
     */
    protected $name = '';

    /**
     * Liste des attributs de configuration de la route.
     * @var array
     */
    protected $attributes = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Identifiant de qualification de la route.
     * @param array $attrs Liste des attributs de configuration de la route.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        parent::__construct();

        $this->name = $name;
        $this->attributes = $attrs;
    }

    /**
     * Récupération d'attribut de configuration.
     *
     * @param string $key Clé d'index de l'attribut.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default  = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Vérifie si le controleur d'appel de la route est une fonction anonyme.
     *
     * @param mixed $callable
     *
     * @return bool
     */
    public function isClosure($cb)
    {
        if (is_string($cb)) :
            return false;
        elseif (is_object($cb)) :
            return $cb instanceof \Closure;
        endif;

        try {
            $reflection = new \ReflectionFunction($cb);

            return $reflection->isClosure();
        } catch (\ReflectionException $e) {
            return false;
        }
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
        $this->appRequest('attributes')->add(
            [
                'tify_route_name' => $this->name,
                'tify_route_args' => $args
            ]
        );

        $cb = $this->get('cb');
        array_push($args, $request, $response);

        if ($this->isClosure($cb)) :
            call_user_func_array($cb, $args);
        else :
            $this->appAddAction(
                'template_redirect',
                function () use ($cb, $args) {
                    $output = call_user_func_array($cb, $args);
                    if (is_string($output)) :
                        $response = end($args);
                        $response->getBody()->write($output);
                        $this->appServiceGet('tfy.route.emitter')->emit($response);
                    endif;

                    exit;
                },
                0
            );
        endif;

        return $response;
    }
}