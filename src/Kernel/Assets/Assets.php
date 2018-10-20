<?php

namespace tiFy\Kernel\Assets;

use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use tiFy\Kernel\Tools;
use tiFy\tiFy;

final class Assets implements AssetsInterface
{
    /**
     * Liste des attributs JS.
     * @var array
     */
    protected $dataJs = [];

    /**
     * Liste des styles css.
     * @var array
     */
    protected $inlineCss = [];

    /**
     * Liste des styles css.
     * @var array
     */
    protected $inlineJs = [];

    /**
     * Liste des librairies tierces CSS +JS
     * @var array
     */
    protected $thirdParty = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->thirdParty = require_once (dirname(__FILE__) . '/third-party.php');

        add_action('init', [$this, 'init'], 10);
        add_action('admin_head', [$this, 'admin_head']);
        add_action('admin_footer', [$this, 'admin_footer']);
        add_action('wp_head', [$this, 'wp_head']);
        add_action('wp_footer', [$this, 'wp_footer']);

        $this->setDataJs('ajax_url', admin_url('admin-ajax.php', 'relative'), 'both', false);
        $this->setDataJs('ajax_response', [], 'both', false);
    }

    /**
     * Définition de styles Css.
     *
     * @param string $css propriétés Css.
     * @param string $ui Interface de l'attribut. user|admin|both
     *
     * @return void
     */
    public function addInlineCss($css, $ui = 'user')
    {
        switch($ui) :
            case 'admin' :
            case 'user' :
                Arr::set($this->inlineCss, $ui, Arr::get($this->inlineCss, $ui, '') . (string)$css);
                break;
            case 'both' :
                Arr::set($this->inlineCss, 'admin', Arr::get($this->inlineCss, 'admin', '') . (string)$css);
                Arr::set($this->inlineCss, 'user', Arr::get($this->inlineCss, 'user', '') . (string)$css);
                break;
        endswitch;
    }

    /**
     * Définition de styles JS.
     *
     * @param string $js propriétés Js.
     * @param string $ui Interface de l'attribut. user|admin|both
     * @param boolean $footer false (défaut) pour inscrire le script dans le header|true pour inscrire le script dans le footer.
     *
     * @return void
     */
    public function addInlineJs($js, $ui = 'user', $footer = false)
    {
        $place = $footer ? '.footer' : '.header';

        switch($ui) :
            case 'admin' :
            case 'user' :
                Arr::set($this->inlineJs, $ui . $place, Arr::get($this->inlineJs, $ui . $place, '') . (string)$js);
                break;
            case 'both' :
                Arr::set($this->inlineJs, "admin{$place}", Arr::get($this->inlineJs, "admin{$place}", '') . (string)$js);
                Arr::set($this->inlineJs, "user{$place}", Arr::get($this->inlineJs, "user{$place}", '') . (string)$js);
                break;
        endswitch;
    }

    /**
     * Scripts de l'entête de l'interface d'administration de Wordpress.
     *
     * @return void
     */
    public function admin_head()
    {
        if ($css = Arr::get($this->inlineCss, 'admin', '')) :
        ?><style type="text/css"><?php echo $css; ?></style><?php
        endif;

        $datas = (new Collection(Arr::get($this->dataJs, 'admin', [])))
            ->where('in_footer', '===', false)
            ->pluck('value', 'key')
            ->all();

        $js = Arr::get($this->inlineJs, 'admin.header', '')

        ?><script type="text/javascript">/* <![CDATA[ */var tify_ajaxurl='<?php echo admin_url('admin-ajax.php', 'relative');?>';<?php echo 'var tify={};'; if($datas) : foreach($datas as $k => $v) : echo "tify['{$k}']=" . \wp_json_encode($v) . ";"; endforeach; endif; echo $js; ?>/* ]]> */</script><?php
    }

    /**
     * Scripts du pied de page de l'interface d'administration de Wordpress.
     *
     * @return void
     */
    public function admin_footer()
    {
        $datas = (new Collection(Arr::get($this->dataJs, 'admin', [])))
            ->where('in_footer', '===', true)
            ->pluck('value', 'key')
            ->all();

        $js = Arr::get($this->inlineJs, 'admin.footer', '');

        if ($datas || $js) :
            ?><script type="text/javascript">/* <![CDATA[ */<?php if($datas) : foreach($datas as $k => $v) : echo "tify['{$k}']=" . \wp_json_encode($v) . ";"; endforeach; endif; echo $js; ?>/* ]]> */</script><?php
        endif;
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style('tiFyAdmin', $this->url('/admin/css/styles.css'), [], 180528);
        \wp_register_script('tiFyAdmin', $this->url('/admin/js/scripts.js'), ['jquery'], 180528, true);

        foreach(Arr::get($this->thirdParty, 'css', []) as $handle => $attrs) :
            \wp_register_style($handle, $attrs[0], $attrs[1], $attrs[2], $attrs[3]);
        endforeach;

        foreach(Arr::get($this->thirdParty, 'js', []) as $handle => $attrs) :
            \wp_register_script($handle, $attrs[0], $attrs[1], $attrs[2], $attrs[3]);
        endforeach;
    }

    /**
     *
     */
    public function setAjaxResponse($key, $value, $context = ['admin', 'user'])
    {
        return $this->setDataJs("ajax_response.{$key}", $value, $context, true);
    }

    /**
     * Définition d'attributs JS.
     *
     * @param string $key Clé d'indexe de l'attribut à ajouter.
     * @param mixed $value Valeur de l'attribut.
     * @param array $context Contexte d'instanciation de l'attribut. user|admin|both
     * @param bool $in_footer Ecriture des attributs dans le pied de page du site.
     *
     * @return void
     */
    public function setDataJs($key, $value, $context = ['admin', 'user'], $in_footer = true)
    {
        if (is_string($context)) :
            $context = (array)$context;
        endif;

        $context = in_array('both', $context) ? ['admin', 'user'] : $context;

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

        foreach($context as $ui) :
            Arr::set($this->dataJs, "{$ui}.{$key}", compact('in_footer', 'key', 'value'));
        endforeach;
    }

    /**
     * Récupération de l'url vers un asset.
     *
     * @param string $path Chemin relatif vers un fichier ou un répertoire.
     *
     * @return string
     */
    public function url($path = '')
    {
        return home_url('vendor/presstify/framework/assets' . ($path ? '/' . ltrim($path, '/') : $path));
    }

    /**
     * Scripts de l'entête de l'interface utilisateur de Wordpress.
     *
     * @return void
     */
    public function wp_head()
    {
        if ($css = Arr::get($this->inlineCss, 'user', '')) :
        ?><style type="text/css"><?php echo $css; ?></style><?php
        endif;

        $datas = (new Collection(Arr::get($this->dataJs, 'user', [])))
                ->where('in_footer', '===', false)
                ->pluck('value', 'key')
                ->all();

        $js = Arr::get($this->inlineJs, 'user.header', '');

        ?><script type="text/javascript">/* <![CDATA[ */var tify_ajaxurl='<?php echo admin_url('admin-ajax.php', 'relative');?>';<?php echo 'var tify={};'; if($datas) : foreach($datas as $k => $v) : echo "tify['{$k}']=". \wp_json_encode($v) . ";"; endforeach; endif; echo $js; ?>/* ]]> */</script><?php
    }

    /**
     * Scripts du pied de page de l'interface utilisateur de Wordpress.
     *
     * @return void
     */
    public function wp_footer()
    {
        $datas = (new Collection(Arr::get($this->dataJs, 'user', [])))
            ->where('in_footer', '===', true)
            ->pluck('value', 'key')
            ->all();

        $js = Arr::get($this->inlineJs, 'user.footer', '');

        if ($datas || $js) :
            ?><script type="text/javascript">/* <![CDATA[ */<?php if($datas) : foreach($datas as $k => $v) : echo "tify['{$k}']=". \wp_json_encode($v) . ";"; endforeach; endif; echo $js; ?>/* ]]> */</script><?php
        endif;
    }
}
