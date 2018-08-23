<?php

namespace tiFy\App\Layout\Request;

use Illuminate\Http\Request;
use tiFy\App\AbstractAppController;
use tiFy\App\Layout\LayoutInterface;

/**
 * Class RequestBaseController
 * @package tiFy\App\Layout\Request
 *
 * @method Request get(string $key, mixed $default = null)
 * @method Request fullUrl()
 */
class RequestBaseController extends AbstractAppController implements RequestInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutInterface;
     */
    protected $app;

    /**
     * Appel des méthodes de requête.
     * @see Request
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $request = $this->app->appRequest();
        if (method_exists($request, $name)) :
            return call_user_func_array([$request, $name], $arguments);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryArgs()
    {
        return $this->app->param('query_args', []);
    }

    /**
     * {@inheritdoc}
     */
    public function sanitizeUrl($remove_query_args = [], $url = '')
    {
        if(empty($remove_query_args)) :
            $remove_query_args = \wp_removable_query_args();
        endif;

        return remove_query_arg($remove_query_args, $url ? : $this->fullUrl());
    }
}