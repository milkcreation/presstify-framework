<?php

namespace tiFy\Apps\Layout\Request;

use Illuminate\Http\Request;
use tiFy\Apps\Layout\LayoutControllerInterface;

class RequestBaseController implements RequestInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutControllerInterface
     */
    protected $app;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des paramètres personnalisés.
     * @param LayoutControllerInterface $app  Classe de rappel du controleur de l'application.
     *
     * @return void
     */
    public function __construct(LayoutControllerInterface $app)
    {
        $this->app = $app;
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
    public function url()
    {
       return $this->app->appRequest()->fullUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function sanitizeUrl($remove_query_args = [], $url = '')
    {
        if(empty($remove_query_args)) :
            $remove_query_args = \wp_removable_query_args();
        endif;

        return remove_query_arg($remove_query_args, $url ? : $this->url());
    }
}