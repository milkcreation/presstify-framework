<?php

/**
 * @name Route
 * @desc Gestionnaire de routage de page
 * @package presstiFy
 * @namespace \tiFy\Route
 * @version 1.1
 * @subpackage Core
 * @since 1.2.596
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Route;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Http\Exception\MethodNotAllowedException;
use League\Route\Strategy\ApplicationStrategy;
use League\Route\Strategy\JsonStrategy;
use League\Route\Strategy\StrategyInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use tiFy\App\AppController;
use tiFy\tiFy;
use tiFy\Route\RouteCollectionController;
use tiFy\Route\RouteCollectionInterface;
use tiFy\Route\RouteHandle;
use Zend\Diactoros\Response\SapiEmitter;

final class Route extends AppController
{
    /**
     * Classe de rappel de la reponse de la requête globale.
     * @var ResponseInterface
     */
    private $response;

    /**
     * {@inheritdoc}
     */
    public function appBoot()
    {
        $this->appServiceShare('tfy.route.response', function () {
            return (new DiactorosFactory())->createResponse(new Response());
        });

        $this->appServiceShare('tfy.route.request', function () {
            return (new DiactorosFactory())->createRequest($this->appRequest());
        });

        $this->appServiceShare('tfy.route.emitter', new SapiEmitter());

        $this->appServiceShare(RouteCollectionInterface::class, new RouteCollectionController(tiFy::instance()));

        $this->appServiceAdd(RouteHandle::class);

        $this->appAddAction('wp_loaded', null, 0);
    }

    /**
     * Récupération du controleur de gestion des routes déclarées.
     *
     * @return RouteCollectionInterface
     */
    public function collection()
    {
        return $this->appServiceGet(RouteCollectionInterface::class);
    }

    /**
     * A l'issue du chargement complet de Wordpress.
     *
     * @return void
     */
    public function wp_loaded()
    {
        foreach($this->appConfig('map', []) as $name => $attrs) :
            $this->register($name, $attrs);
        endforeach;

        do_action('tify_route_register', $this);

        if ($this->appConfig('remove_trailing_slash', true)) :
            /**
             * Suppression du slash de fin dans l'url
             * @see https://symfony.com/doc/current/routing/redirect_trailing_slash.html
             * @see https://stackoverflow.com/questions/30830462/how-to-deal-with-extra-in-phpleague-route
             *
             * @var Request $request
             */
            $request = $this->appRequest();
            $path = $request->getBaseUrl() . $request->getPathInfo();

            if(
                ($path != '/') &&
                (substr($path, -1) == '/') &&
                ($request->getMethod() === 'GET') &&
                ((new Collection($this->collection()->all()))->first(
                    function($route) use ($path) {
                        /** @var RouteInterface $route */
                        return (in_array('GET', $route->getMethods()) && preg_match('#^'. preg_quote($route->getPath(), '/') . '/$#', $path));
                    })
                )
            ) :
                wp_safe_redirect($request->fullUrl(), 301);
                exit;
            endif;
        endif;

        // Traitement des routes
        try {
            $this->response = $this->collection()->dispatch(
                $this->appServiceGet('tfy.route.request'),
                $this->appServiceGet('tfy.route.response')
            );
        } catch (NotFoundException $e) {

        } catch (MethodNotAllowedException $e) {

        }

        return;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Déclaration
     *
     * @param string $name Identifiant de qualification de la route
     * @param array $attrs Attributs de configuration
     *
     * @return null|array
     */
    public function register($name, $attrs = [])
    {
        $attrs = array_merge(
            [
                'method'   => 'any',
                'group'    => '',
                'path'     => '/',
                'cb'       => '',
                'strategy' => ''
            ],
            $attrs
        );

        /**
         * @var string|array $method
         * @var string $group
         * @var string $path
         * @var string $cb
         * @var string|object $strategy
         */
        extract($attrs);

        // Traitement du sous repertoire
        $path = $this->appRequest()->getBaseUrl() . $path;

        // Traitement de la méthode
        $method = ($method === 'any') ? ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'] : array_map('strtoupper', (array)$method);

        $scheme = $this->appRequest()->getScheme();
        $host = $this->appRequest()->getHost();

        $strategy = $strategy ? : new ApplicationStrategy();
        switch($strategy) :
            case 'app' :
            case 'html' :
                $strategy = new ApplicationStrategy();
                break;
            case 'json' :
                $strategy = new JsonStrategy();
                break;
        endswitch;
        if (!$strategy instanceof StrategyInterface) :
            \wp_die(
                sprintf(
                    __('La stratégie de sortie de la route %s devrait être une instance de %s', 'tify'),
                    $name,
                    StrategyInterface::class
                ),
                __('tiFy\Route\Route: Stratégie invalide', 'tify'),
                500
            );
        endif;

        return $this->collection()->map(
            $method,
            $path,
            $this->appServiceGet(RouteHandle::class, [$name, $attrs, app()])
        )
            ->setName($name)
            ->setScheme($scheme)
            ->setHost($host)
            ->setStrategy($strategy);
    }

    /**
     * Vérifie la correspondance du nom de qualification d'une route existante avec la valeur soumise.
     *
     * @param string $name Identifiant de qualification de la route à vérifier
     *
     * @return bool
     */
    public function exists($name)
    {
        return !empty($this->collection()->getNamedRoute($name));
    }

    /**
     * Récupération de l'url d'une route déclarée
     *
     * @param string $name Identifiant de qualification de la route
     * @param array $replacements Arguments de remplacement
     *
     * @return string
     */
    public function url($name, $replacements = [])
    {
        try {
            $route = $this->collection()->getNamedRoute($name);
            $host = $route->getHost();
            $port = $this->appRequest()->getPort();
            $scheme = $route->getScheme();
            $path = $route->getPath();
            if ((($port === 80) && ($scheme = 'http')) || (($port === 443) && ($scheme = 'https'))) :
                $port = '';
            endif;

            $url = $scheme . '://' . $host . ($port ? ':' . $port : '') . $path;
            $url = preg_replace_callback(
                '#{(\w+|\d+)}#',
                function($matches) use (&$replacements) {
                    return array_shift($replacements);
                },
                $url
            );

            return $url;
        } catch (InvalidArgumentException $e) {
            return '';
        }
    }

    /**
     * Redirection de page vers une route déclarée.
     *
     * @param string $name Identifiant de qualification de la route
     * @param array $args Liste arguments passés en variable de requête dans l'url
     * @param int $status_code Code de redirection. @see https://fr.wikipedia.org/wiki/Liste_des_codes_HTTP
     *
     * @return void
     */
    public function redirect($name, $args = [], $status_code = 301)
    {
        if ($redirect_url = $this->url($name, $args)) :
            \wp_redirect($redirect_url, $status_code);
            exit;
        endif;
    }

    /**
     * Récupération du nom de qualification de la route courante à afficher.
     *
     * @return string
     */
    public function currentName()
    {
        return $this->appRequest('attributes')->get('tify_route_name', '');
    }

    /**
     * Récupération des arguments de requête passés dans la route courante.
     *
     * @return array
     */
    public function currentArgs()
    {
        return $this->appRequest('attributes')->get('tify_route_args', []);
    }

    /**
     * Vérifie la correspondance du nom de qualification la route courante avec la valeur soumise.
     *
     * @param string $name Identifiant de qualification de la route à vérifier
     *
     * @return bool
     */
    public function isCurrent($name)
    {
        return $name === $this->currentName();
    }

    /**
     * Vérifie si la page d'affichage courante correspond à une route déclarée.
     *
     * @return bool
     *
     * @throws LogicException
     */
    public function is()
    {
        global $wp_query;

        if (!$wp_query instanceof \WP_Query) :
            throw new LogicException(
                __('Cette méthode est appelée de la mauvaise manière, elle devrait être déclenchée après l\'action "pre_get_posts', 'tify'),
                500
            );
        endif;

        return isset($wp_query->is_route) && ($wp_query->is_route === true);
    }

    /**
     * Vérifie si la page d'affichage courante correspond à une route déclarée.
     *
     * @return bool
     */
    public function hasCurrent()
    {
        return !empty($this->currentName());
    }
}