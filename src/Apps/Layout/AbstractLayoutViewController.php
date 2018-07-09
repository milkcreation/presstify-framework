<?php

namespace tiFy\Apps\Layout;

use tiFy\Apps\AppController;

abstract class AbstractLayoutViewController extends AppController implements LayoutViewInterface
{
    /**
     * Alias de récupération du service de controleur d'affichage.
     * @var string
     */
    protected $layoutAlias = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param string $alias Alias de récupération du service de controleur d'affichage.
     *
     * @return void
     */
    public function __construct($alias)
    {
        parent::__construct();

        $this->layoutAlias = $alias;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnv()
    {
        return $this->isAdmin() ? 'admin' : 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function isAdmin()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function layout()
    {
        return $this->appServiceGet($this->layoutAlias);
    }
}