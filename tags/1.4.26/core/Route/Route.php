<?php

/**
 * @name Route
 * @desc Gestionnaire de routage de page
 * @package presstiFy
 * @namespace \tiFy\Core\Route
 * @version 1.1
 * @subpackage Core
 * @since 1.2.596
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Core\Route;

use tiFy\tiFy;
use League\Container\Container;
use League\Route\RouteCollection;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Http\Exception\MethodNotAllowedException;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Zend\Diactoros\Response\SapiEmitter;
use InvalidArgumentException;

class Route extends \tiFy\App\Core
{
    /**
     * Classe de rappel du conteneur d'injection de dépendances.
     * @var Container
     */
    private $container;

    /**
     * Classe de rappel de la reponse de la requête globale.
     * @var ResponseInterface
     */
    private $response;

    /**
     * Cartographie des routes déclarées.
     * @var array
     */
    protected $map = [];

    /**
     * Options d'activation de suppression du slash à la fin de l'url.
     * @var bool
     */
    private $removeTrailingSlash = false;

    /**
     * Indicateur de contexte d'affichage de page de Wordpress.
     * @var string[]
     */
    protected $conditionnalTags = [
        'is_single',
        'is_preview',
        'is_page',
        'is_archive',
        'is_date',
        'is_year',
        'is_month',
        'is_day',
        'is_time',
        'is_author',
        'is_category',
        'is_tag',
        'is_tax',
        'is_search',
        'is_feed',
        'is_comment_feed',
        'is_trackback',
        'is_home',
        'is_404',
        'is_embed',
        'is_paged',
        'is_admin',
        'is_attachment',
        'is_singular',
        'is_robots',
        'is_posts_page',
        'is_post_type_archive'
    ];

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Déclaration des dépendances
        $this->container = new Container();

        // Définition du traitement de la réponse
        $this->container->share('tiFy.core.route.response', function () {
            $response = new Response;

            return (new DiactorosFactory())->createResponse($response);
        });

        // Définition du traitement de la requête
        $this->container->share('tiFy.core.route.request', function () {
            return (new DiactorosFactory())->createRequest(tiFy::getGlobalRequest());
        });

        // Définition du traitement de l'affichage
        $this->container->share('tiFy.core.route.emitter', new SapiEmitter());

        // Définition du traitement des routes
        $this->container->share('tiFy.core.route.collection', new RouteCollection($this->container));

        // Déclaration des événements
        $this->appAddAction('init', null, 0);
        $this->appAddAction('pre_get_posts', null, 0);

        // Instanciation des fonctions d'aide à la saisie
        require_once $this->appDirname() . '/Helpers.php';
    }

    /**
     * EVENEMENTS
     */
    /**
     * Initialisation globale
     *
     * @return void
     */
    public function init()
    {
        do_action('tify_route_register');

        // Bypass
        if (!$this->map) :
            return;
        endif;

        // Définition de la cartographie des routes
        foreach ($this->map as $name => $attrs) :
            $this->_set($name);
        endforeach;

        if ($this->removeTrailingSlash) :
            /**
             * Suppression du slash de fin dans l'url
             * @see https://symfony.com/doc/current/routing/redirect_trailing_slash.html
             * @see https://stackoverflow.com/questions/30830462/how-to-deal-with-extra-in-phpleague-route
             *
             * @var Request $request
             */
            $request = tiFy::getGlobalRequest();

            $pathInfo = $request->getPathInfo();
            $requestUri = $request->getRequestUri();
            $method = $request->getMethod();

            if (($pathInfo != '/') && (substr($pathInfo, -1) == '/') && ($method === 'GET')) :

                $url = str_replace($pathInfo, rtrim($pathInfo, '/'), $requestUri);
                wp_safe_redirect($url, 301);
                exit;
            endif;
        endif;

        // Traitement des routes
        try {
            $this->response = $this->getContainer('collection')->dispatch(
                $this->container->get('tiFy.core.route.request'),
                $this->container->get('tiFy.core.route.response')
            );
        } catch (NotFoundException $e) {

        } catch (MethodNotAllowedException $e) {

        }

        return;
    }

    /**
     * Pré-traitement de la requête de récupération de post WP.
     *
     * @param \WP_Query $wp_query Classe de rappel de traitement de requête Wordpress.
     *
     * @return void
     */
    public function pre_get_posts(&$wp_query)
    {
        if (self::hasCurrent() && $wp_query->is_main_query()) :
            foreach($this->conditionnalTags as $ct) :
                $wp_query->{$ct} = false;
            endforeach;
            $wp_query->query_vars = $wp_query->fill_query_vars([]);
            $wp_query->is_route = true;
        endif;
    }

    /**
     * Définition d'une route
     *
     * @param string $name Identifiant de qualification de la route
     *
     * @return null|\League\Route\Route
     */
    private function _set($name)
    {
        // Bypass
        if (!isset($this->map[$name])) :
            return null;
        endif;

        /**
         * @var string|array $method
         * @var string $group
         * @var string $path
         * @var string $cb
         */
        extract($this->map[$name]);

        // Traitement du sous repertoire
        $path = ($sub = trim(basename(dirname($_SERVER['PHP_SELF'])), '/')) ? "/{$sub}/" . ltrim($path, '/') : $path;

        // Traitement de la méthode
        $method = ($method === 'any') ? ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'] : array_map('strtoupper', (array)$method);

        $scheme = tiFy::getGlobalRequest()->getScheme();
        $host = tiFy::getGlobalRequest()->getHost();

        return $this->getContainer('collection')->map(
            $method,
            $path,
            new Handler($name, $this->map[$name])
        )
            ->setName($name)
            ->setScheme($scheme)
            ->setHost($host);
    }

    /**
     * Récupération du conteneur d'injection de dépendances
     *
     * @param string $alias response|request|emitter|collection
     *
     * @return Container
     */
    final public function getContainer($alias)
    {
        return $this->container->get('tiFy.core.route.' . $alias);
    }

    /**
     * @return mixed
     */
    final public function getResponse()
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
     *
     * @throws
     */
    final public static function register($name, $attrs = [])
    {
        /**
         * @var \tiFy\Core\Route\Route $instance
         */
        if (!$instance = self::tFyAppGetContainer('tiFy\Core\Route\Route')) :
            return null;
        endif;

        $defaults = [
            'method'   => 'any',
            'group'    => '',
            'path'     => '/',
            'cb'       => '',
            'strategy' => ''
        ];

        return $instance->map[$name] = array_merge($defaults, $attrs);
    }

    /**
     * Vérifie la correspondance du nom de qualification d'une route existante avec la valeur soumise.
     *
     * @param string $name Identifiant de qualification de la route à vérifier
     *
     * @return bool
     */
    final public static function exists($name)
    {
        /**
         * @var \tiFy\Core\Route\Route $instance
         */
        if (!$instance = self::tFyAppGetContainer('tiFy\Core\Route\Route')) :
            return false;
        endif;

        return isset($instance->map[$name]);
    }

    /**
     * Récupération de l'url d'une route déclarée
     *
     * @param string $name Identifiant de qualification de la route
     * @param array $replacements Arguments de remplacement
     *
     * @return string
     */
    final public static function url($name, $replacements = [])
    {
        /**
         * @var \tiFy\Core\Route\Route $instance
         */
        if (!$instance = self::tFyAppGetContainer('tiFy\Core\Route\Route')) :
            return '';
        endif;

        try {
            $router = $instance->getContainer('collection');
            $route = $router->getNamedRoute($name);
            $host = $route->getHost();
            $port = tiFy::getGlobalRequest()->getPort();
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
    final public static function redirect($name, $args = [], $status_code = 301)
    {
        if ($redirect_url = self::url($name, $args)) :
            \wp_redirect($redirect_url, $status_code);
            exit;
        endif;
    }

    /**
     * Récupération du nom de qualification de la route courante à afficher.
     *
     * @return string
     */
    final public static function currentName()
    {
        return (string)self::tFyAppGetRequestVar('tify_route_name', '', 'ATTRIBUTES');
    }

    /**
     * Récupération des arguments de requête passés dans la route courante.
     *
     * @return array
     */
    final public static function currentArgs()
    {
        return (array)self::tFyAppGetRequestVar('tify_route_args', [], 'ATTRIBUTES');
    }

    /**
     * Vérifie la correspondance du nom de qualification la route courante avec la valeur soumise.
     *
     * @param string $name Identifiant de qualification de la route à vérifier
     *
     * @return bool
     */
    final public static function isCurrent($name)
    {
        return ($name === self::currentName());
    }

    /**
     * Vérifie si la page d'affichage courante correspond à une route déclarée.
     *
     * @return bool
     *
     * @throws LogicException
     */
    final public static function is()
    {
        global $wp_query;

        if (!did_action('pre_get_posts')) :
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
    final public static function hasCurrent()
    {
        return (self::currentName() !== '');
    }
}