<?php
namespace tiFy\Core\Ui\Admin\Templates\Browser;

use tiFy\Core\Ui\Ui;
use tiFy\Core\Control\Control;
use tiFy\Lib\Stream\Local\Filesystem as Stream;
use Symfony\Component\Filesystem\Filesystem;

class Browser extends \tiFy\Core\Ui\Admin\Factory
{
    /**
     * Classe de rappel du système de fichier
     * @return \tiFy\Lib\Stream\Local\Filesystem
     */
    protected $Stream = null;

    /**
     * Récupération Ajax de l'aperçu d'une image
     *
     * @return string
     */
    public function ajaxGetImagePreview()
    {
        $filename = $_POST['filename'];

        if (!preg_match("#^". preg_quote(ABSPATH, '/') ."#", $filename)) :
            $mime_type = \mime_content_type($filename);
            $data = \base64_encode(file_get_contents($filename));
            $src = "data:image/{$mime_type};base64,{$data}";
        else :
            $rel = preg_replace("#^". preg_quote(ABSPATH, '/') ."#", '', $filename);
            $src = \site_url($rel);
        endif;

        wp_send_json(compact('src'));
    }
}