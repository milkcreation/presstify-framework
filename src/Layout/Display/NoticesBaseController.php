<?php

namespace tiFy\Layout\Display;

use tiFy\Contracts\Layout\LayoutDisplayNoticesInterface;
use tiFy\Contracts\Layout\LayoutDisplayInterface;
use tiFy\Kernel\Parameters\AbstractParametersBag;

class NoticesBaseController extends AbstractParametersBag implements LayoutDisplayNoticesInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutDisplayInterface
     */
    protected $layout;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs
     */
    public function __construct(LayoutDisplayInterface $layout)
    {
        $this->layout = $layout;

        parent::__construct([]);
    }

    /**
     * Affichage des message de notifications de l'interface d'affichage.
     *
     * return void|string
     */
    public function display()
    {
        if ($notice = $this->all()) :
            ?><div class="notice notice-<?php echo $notice['notice'];?><?php echo $notice['dismissible'] ? ' is-dismissible':'';?>"><p><?php echo $notice['message'] ?></p></div><?php
        endif;
    }
}