<?php

namespace tiFy\AdminView;

use tiFy\AdminView\Interop\AbstractAttributesAwareController;

class AdminViewNoticesController extends AbstractAttributesAwareController implements AdminViewNoticesControllerInterface
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('admin_notices');
    }

    /**
     * Affichage des message de notifications de l'interface d'administration.
     *
     * return void|string
     */
    public function admin_notices()
    {
        if ($notice = $this->getNotice()) :
            ?><div class="notice notice-<?php echo $notice['notice'];?><?php echo $notice['dismissible'] ? ' is-dismissible':'';?>"><p><?php echo $notice['message'] ?></p></div><?php
        endif;
    }
}