<?php

namespace tiFy\Layout\Display;

use tiFy\Contracts\Layout\LayoutDisplayRequestInterface;
use tiFy\Contracts\Layout\LayoutDisplayInterface;
use tiFy\Kernel\Http\Request;

/**
 * Class RequestBaseController
 *
 * @mixin Request
 */
class RequestBaseController implements LayoutDisplayRequestInterface
{
    /**
     * Instance du controleur de la disposition associée.
     * @var LayoutDisplayInterface;
     */
    protected $layout;

    /**
     * CONSTRUCTEUR.
     *
     * @param LayoutDisplayInterface Instance du controleur de la disposition associée.
     *
     * @return void
     */
    public function __construct(LayoutDisplayInterface $layout)
    {
        $this->layout = $layout;
    }

    /**
     * Appel des méthodes de requête.
     * @see Request
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $request = request();
        if (method_exists($request, $name)) :
            return call_user_func_array([$request, $name], $arguments);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryArgs()
    {
        return $this->layout->param('query_args', []);
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