<?php

namespace tiFy\AdminView\Notice;

use tiFy\AdminView\AdminViewControllerInterface;
use tiFy\Apps\Attributes\AbstractAttributesController;

class NoticeCollectionBaseController extends AbstractAttributesController implements NoticeCollectionInterface
{
    /**
     * Classe de rappel du controleur de l'interface d'administration associée.
     * @var AdminViewControllerInterface
     */
    protected $app;

    /**
     * Affichage des message de notifications de l'interface d'administration.
     *
     * return void|string
     */
    public function admin_notices()
    {
        if ($notice = $this->all()) :
            ?><div class="notice notice-<?php echo $notice['notice'];?><?php echo $notice['dismissible'] ? ' is-dismissible':'';?>"><p><?php echo $notice['message'] ?></p></div><?php
        endif;
    }
}