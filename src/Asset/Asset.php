<?php

namespace tiFy\Asset;

use tiFy\Apps\AppController;

final class Asset extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('admin_head', [$this, 'ajaxUrl']);
        $this->appAddAction('wp_head', [$this, 'ajaxUrl']);
    }

    /**
     * Variable global de l'url des actions Ajax des scripts tiFy.
     *
     * @return void
     */
    public function ajaxUrl()
    {
        ?><script type="text/javascript">/* <![CDATA[ */var tify_ajaxurl='<?php echo admin_url('admin-ajax.php', 'relative');?>';/* ]]> */</script><?php
    }
}
