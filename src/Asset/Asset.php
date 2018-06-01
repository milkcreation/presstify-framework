<?php

namespace tiFy\Asset;

use Illuminate\Support\Arr;
use tiFy\Apps\AppController;

final class Asset extends AppController
{
    /**
     * Liste des attributs JS.
     * @return array
     */
    protected $dataJs = [];

    /**
     * Liste des styles CSS.
     * @return array
     */
    protected $inlineCSS = [];

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('init');
        $this->appAddAction('admin_head');
        $this->appAddAction('wp_head');

        $this->setDataJs('ajax_url', admin_url('admin-ajax.php', 'relative'), 'both');
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_script('tiFyAdmin', $this->appAsset('/Admin/js/scripts.js'), ['jquery'], 180528, true);
        \wp_register_style('tiFyAdmin', $this->appAsset('/Admin/css/styles.css'), [], 180528);
    }

    /**
     * Entête de l'interface d'administration de Wordpress.
     *
     * @return void
     */
    public function admin_head()
    {
        if ($css = Arr::get($this->inlineCSS, 'admin', '')) :
        ?><style type="text/css"><?php echo $css; ?></style><?php
        endif;

        if ($js = Arr::get($this->dataJs, 'admin', [])) :
        ?><script type="text/javascript">/* <![CDATA[ */var tify_ajaxurl='<?php echo admin_url('admin-ajax.php', 'relative');?>';<?php echo 'var tFy=' . wp_json_encode($js) . ';'; ?>/* ]]> */</script><?php
        endif;
    }

    /**
     * Entête de l'interface utilisateur de Wordpress.
     *
     * @return void
     */
    public function wp_head()
    {
        if ($css = Arr::get($this->inlineCSS, 'user', '')) :
        ?><style type="text/css"><?php echo $css; ?></style><?php
        endif;

        if ($js = Arr::get($this->dataJs, 'user', [])) :
        ?><script type="text/javascript">/* <![CDATA[ */var tify_ajaxurl='<?php echo admin_url('admin-ajax.php', 'relative');?>';<?php echo 'var tFy=' . wp_json_encode($js) . ';'; ?>/* ]]> */</script><?php
        endif;
    }

    /**
     * Définition d'attributs JS.
     *
     * @param string $key Clé d'indexe de l'attribut à ajouter.
     * @param mixed $value Valeur de l'attribut.
     * @param string $ui Interface de l'attribut. user|admin|both
     *
     * @return void
     */
    public function setDataJs($key, $value, $ui = 'user')
    {
        if (is_array($value)) :
            foreach($value as $k => &$v) :
                if (!is_scalar($v)) :
                    continue;
                endif;

                $v = html_entity_decode((string)$v, ENT_QUOTES, 'UTF-8');
            endforeach;
        elseif(is_scalar($value)) :
            $value = html_entity_decode((string)$value, ENT_QUOTES, 'UTF-8');
        endif;

        switch($ui) :
            case 'admin' :
            case 'user' :
                Arr::set($this->dataJs, "{$ui}.{$key}", $value);
                break;
            case 'both' :
                Arr::set($this->dataJs, "admin.{$key}", $value);
                Arr::set($this->dataJs, "user.{$key}", $value);
                break;
        endswitch;
    }

    /**
     * Définition de styles CSS.
     *
     * @param string $css propriétés CSS.
     * @param string $ui Interface de l'attribut. user|admin|both
     *
     * @return void
     */
    public function addInlineCss($css, $ui = 'user')
    {
        switch($ui) :
            case 'admin' :
            case 'user' :
                Arr::set($this->inlineCSS, $ui, Arr::get($this->inlineCSS, $ui, '') . (string)$css);
                break;
            case 'both' :
                Arr::set($this->inlineCSS, 'admin', Arr::get($this->inlineCSS, 'admin', '') . (string)$css);
                Arr::set($this->inlineCSS, 'user', Arr::get($this->inlineCSS, 'user', '') . (string)$css);
                break;
        endswitch;
    }
}
