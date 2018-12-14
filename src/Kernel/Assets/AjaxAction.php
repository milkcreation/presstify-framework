<?php

namespace tiFy\AjaxAction;

use tiFy\App\AppController;
use tiFy\Lib\Video\Video;

final class AjaxAction extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('wp_ajax_tiFyVideoGetEmbed', [$this, 'videoGetEmbed']);
        $this->appAddAction('wp_ajax_nopriv_tiFyVideoGetEmbed', [$this, 'videoGetEmbed']);
    }

    /**
     * Récupération du code d'intégration d'une vidéo.
     *
     * @return string
     */
    final public function videoGetEmbed()
    {
        if (empty($_REQUEST['attr']['src'])) :
            die(0);
        endif;

        wp_die(Video::getEmbed($_REQUEST['attr']));
    }
}