<?php

namespace tiFy\Route;

use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use tiFy\Apps\AppController;
use tiFy\Route\Route;
use tiFy\Route\View;

class Handler extends AppController
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
     * Valeur de retour du controleur.
     * @var string
     */
    private $return;

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
     * Traitement de l'affichage de l'interface utilisateur.
     *
     * @return string
     */
    final public function template_redirect()
    {
        /**
         * Bypass
         * @var \tiFy\Route\Route $route
         */
        if (!$route = $this->appServiceGet(Route::class)) :
            return;
        endif;

        if (!$response = $route->getResponse()) :
            return;
        endif;

        // Récupération de la sortie
        $body = '';
        if ($this->return instanceof View) :
            $body = $this->return->render();
        elseif(is_string($this->return)) :
            $body = $this->return;
        endif;

        // Déclaration de la sortie
        $response->getBody()->write($body);

        // Affichage de la sortie
        $route->getContainer('emitter')->emit($response);
        exit;
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // Définition des attribut de requête de la route courante
        $this->appRequest('attributes')->add(
            [
                'tify_route_name' => $this->name,
                'tify_route_args' => $args
            ]
        );

        // Appel du controleur de route
        $cb = $this->get('cb');

        // Ajout de la requête et de la réponse HTTP (PSR-7) à la liste des arguments
        array_push($args, $request, $response);

        if (is_callable($cb)) :
            $this->return = call_user_func_array($cb, $args);
        elseif(class_exists($cb)) :
            $reflection = new \ReflectionClass($cb);
            $this->return = $reflection->newInstanceArgs($args);
        endif;

        // Instanciation de traitement du retour
        $this->appAddAction('template_redirect', null, 0);

        return $response;
    }
}