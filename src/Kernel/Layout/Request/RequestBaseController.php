<?php

namespace tiFy\Kernel\Layout\Request;

use Illuminate\Http\Request;
use tiFy\Kernel\Layout\LayoutControllerInterface;

class RequestBaseController implements RequestInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutControllerInterface
     */
    protected $app;

    /**
     * Classe de rappel du controleur de requête Http.
     * @var Request
     */
    protected $request;

    /**
     * Url de la page courante
     * @var string
     */
    protected $currentUrl = '';

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
        $this->request = $this->app->appRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function currentUrl()
    {
        if ($this->currentUrl) :
            return $this->currentUrl;
        endif;

        return $this->currentUrl = $this->request->fullUrl();
    }
}