<?php

namespace tiFy\Apps\Layout\Notices;

use tiFy\Apps\Item\AbstractAppItemController;
use tiFy\Apps\Layout\LayoutInterface;

class NoticesBaseController extends AbstractAppItemController implements NoticesInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutInterface
     */
    protected $app;

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