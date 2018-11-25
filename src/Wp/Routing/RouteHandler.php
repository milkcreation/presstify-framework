<?php

namespace tiFy\Wp\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use tiFy\Contracts\View\ViewController;
use tiFy\Routing\RouteHandler as tiFyRouteHandler;
use tiFy\Kernel\Http\RedirectResponse;

class RouteHandler extends tiFyRouteHandler
{
    /**
     * Indicateur de contexte d'affichage de page de Wordpress.
     * @var string[]
     */
    protected $cTags = [
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->router->setCurrent($this->name, $args);

        add_action(
            'pre_get_posts',
            function (\WP_Query &$wp_query) {
                if ($wp_query->is_main_query() && ! $wp_query->is_admin()) :
                    foreach($this->cTags as $ct) :
                        $wp_query->{$ct} = false;
                    endforeach;

                    if ($query_args = $this->get('query_args', [])) :
                        $wp_query->parse_query($query_args);
                    else :
                        $wp_query->query_vars = $wp_query->fill_query_vars([]);
                    endif;

                    $wp_query->is_route = true;
                endif;
            },
            0
        );

        $callback = $this->get('cb');
        if (is_callable($callback)) :
            array_push($args, $request, $response);
            $resolved = call_user_func_array($callback, $args);

            if ($resolved instanceof ViewController) :
                add_action(
                    'template_redirect',
                    function () use ($resolved, $response) {
                        $response->getBody()->write($resolved->render());
                        $this->router->emit($response);
                        exit;
                    },
                    1
                );
            else :
                $response->getBody()->write((string)$resolved);
            endif;
        endif;

        return $response;
    }
}