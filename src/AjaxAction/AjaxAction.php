<?php

namespace tiFy\AjaxAction;

use tiFy\Apps\AppController;
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
        $this->appAddAction('wp_ajax_tify_get_post_permalink', [$this, 'getPostPermalink']);
        $this->appAddAction('wp_ajax_tiFyVideoGetEmbed', [$this, 'videoGetEmbed']);
        $this->appAddAction('wp_ajax_nopriv_tiFyVideoGetEmbed', [$this, 'videoGetEmbed']);
    }

    /**
     * Récupération d'un permalien de post selon son ID.
     *
     * @return string
     */
    public function getPostPermalink()
    {
        // Arguments par defaut à passer en $_POST
        $args = [
            'post_id'  => 0,
            'relative' => true,
            'default'  => site_url('/'),
        ];
        extract($args);

        // Traitement des arguments de requête
        if (isset($_POST['post_id'])) :
            $post_id = intval($_POST['post_id']);
        endif;
        if (!empty($_POST['relative'])) :
            $relative = $_POST['relative'];
        endif;
        if (isset($_POST['default'])) :
            $default = $_POST['default'];
        endif;

        // Traitement du permalien
        $permalink = ($_permalink = get_permalink($post_id)) ? $_permalink : $default;
        if ($relative) :
            $url_path = parse_url(site_url('/'), PHP_URL_PATH);
            $permalink = $url_path . preg_replace('/' . preg_quote(site_url('/'), '/') . '/', '', $permalink);
        endif;

        wp_die($permalink);
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