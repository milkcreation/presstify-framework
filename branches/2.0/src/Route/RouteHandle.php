<?php

namespace tiFy\Route;

use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use tiFy\App\Item\AbstractAppItemController;
use tiFy\Contracts\App\AppInterface;
use tiFy\Contracts\Views\ViewInterface;

class RouteHandle extends AbstractAppItemController
{
    /**
     * Nom de qualification de la route.
     * @var string
     */
    protected $name = '';

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
     * CONSTRUCTEUR.
     *
     * @param string $name Identifiant de qualification de la route.
     * @param array $attrs Liste des attributs de configuration de la route.
     * @param  $app .
     *
     * @return void
     */
    public function __construct($name, $attrs = [], AppInterface $app)
    {
        $this->name = $name;

        parent::__construct($attrs, $app);
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
        elseif (is_array($cb)) :
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
        $this->app->appRequest('attributes')->add(
            [
                'tify_route_name' => $this->name,
                'tify_route_args' => $args
            ]
        );

        foreach($args as $key => $value) :
            $this->app->appRequest($request->getMethod())->set($key, $value);
        endforeach;

        $cb = $this->get('cb');
        array_push($args, $request, $response);

        if ($this->isClosure($cb)) :
            $resolved = call_user_func_array($cb, $args);

            if ($resolved instanceof ViewInterface) :
                add_action(
                    'template_redirect',
                    function () use ($resolved) {
                        $resolved->render();
                        exit;
                    },
                    0
                );
            endif;
        else :
            add_action(
                'template_redirect',
                function () use ($cb, $args) {
                    $output = call_user_func_array($cb, $args);
                    if (is_string($output)) :
                        $response = end($args);
                        $response->getBody()->write($output);
                        $this->app->appServiceGet('tfy.route.emitter')->emit($response);
                    endif;

                    exit;
                },
                0
            );
        endif;

        $this->app->appAddAction('pre_get_posts', [$this, 'pre_get_posts'], 0);

        return $response;
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
        if ($wp_query->is_main_query() && ! $wp_query->is_admin()) :
            foreach($this->conditionnalTags as $ct) :
                $wp_query->{$ct} = false;
            endforeach;

            if ($query_args = $this->get('query_args', [])) :
                $wp_query->parse_query($query_args);
            else :
                $wp_query->query_vars = $wp_query->fill_query_vars([]);
            endif;

            $wp_query->is_route = true;
        endif;
    }
}