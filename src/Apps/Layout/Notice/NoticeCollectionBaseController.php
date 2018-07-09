<?php

namespace tiFy\Apps\Layout\Notice;

use tiFy\Apps\Layout\LayoutControllerInterface;
use tiFy\Apps\Item\AbstractAppItemController;

class NoticeCollectionBaseController extends AbstractAppItemController implements NoticeCollectionInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutControllerInterface
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