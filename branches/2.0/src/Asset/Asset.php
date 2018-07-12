<?php

namespace tiFy\Asset;

use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use tiFy\Apps\AppController;

final class Asset extends AppController
{
    /**
     * Liste des librairies tierces CSS
     * @var array
     */
    protected $cssLib = [
        'datatables' => [
            '//cdn.datatables.net/1.10.18/css/jquery.dataTables.min.css',
            [],
            '1.10.19',
            'screen'
        ]
    ];

    /**
     * Liste des librairies tierces JS
     * @var array
     */
    protected $jsLib = [
        'datatables' => [
            '//cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js',
            ['jquery'],
            '1.10.19',
            true
        ],
        'moment' => [
            '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js',
            [],
            '2.18.1',
            true
        ]
    ];

    /**
     * Liste des attributs JS.
     * @var array
     */
    protected $dataJs = [];

    /**
     * Liste des styles CSS.
     * @var array
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
        $this->appAddAction('admin_head', null);
        $this->appAddAction('admin_print_footer_scripts', null);
        $this->appAddAction('wp_head', null);
        $this->appAddAction('wp_footer', null);

        $this->setDataJs('ajax_url', admin_url('admin-ajax.php', 'relative'), 'both', false);
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style('tiFyAdmin', $this->appAsset('/Admin/css/styles.css'), [], 180528);
        \wp_register_script('tiFyAdmin', $this->appAsset('/Admin/js/scripts.js'), ['jquery'], 180528, true);

        foreach($this->cssLib as $handle => $attrs) :
            \wp_register_style($handle, $attrs[0], $attrs[1], $attrs[2], $attrs[3]);
        endforeach;

        foreach($this->jsLib as $handle => $attrs) :
            \wp_register_script($handle, $attrs[0], $attrs[1], $attrs[2], $attrs[3]);
        endforeach;
    }

    /**
     * Scripts de l'entête de l'interface d'administration de Wordpress.
     *
     * @return void
     */
    public function admin_head()
    {
        if ($css = Arr::get($this->inlineCSS, 'admin', '')) :
        ?><style type="text/css"><?php echo $css; ?></style><?php
        endif;

        $datas = (new Collection(Arr::get($this->dataJs, 'admin', [])))
            ->where('in_footer', '===', false)
            ->pluck('value', 'key')
            ->all();

        ?><script type="text/javascript">/* <![CDATA[ */var tify_ajaxurl='<?php echo admin_url('admin-ajax.php', 'relative');?>';<?php echo 'var tify={};'; ?><?php foreach($datas as $k => $v) : echo "tify['{$k}'] =". \wp_json_encode($v); endforeach; ?>/* ]]> */</script><?php
    }

    /**
     * Scripts du pied de page de l'interface d'administration de Wordpress.
     *
     * @return void
     */
    public function admin_print_footer_scripts()
    {
        $datas = (new Collection(Arr::get($this->dataJs, 'admin', [])))
            ->where('in_footer', '===', true)
            ->pluck('value', 'key')
            ->all();

        if ($datas) :
            ?><script type="text/javascript">/* <![CDATA[ */<?php foreach($datas as $k => $v) : echo "tify['{$k}'] =". \wp_json_encode($v); endforeach; ?>/* ]]> */</script><?php
        endif;
    }

    /**
     * Scripts de l'entête de l'interface utilisateur de Wordpress.
     *
     * @return void
     */
    public function wp_head()
    {
        if ($css = Arr::get($this->inlineCSS, 'user', '')) :
        ?><style type="text/css"><?php echo $css; ?></style><?php
        endif;

        $datas = (new Collection(Arr::get($this->dataJs, 'user', [])))
                ->where('in_footer', '===', false)
                ->pluck('value', 'key')
                ->all();

        ?><script type="text/javascript">/* <![CDATA[ */var tify_ajaxurl='<?php echo admin_url('admin-ajax.php', 'relative');?>';<?php echo 'var tify={};'; ?><?php foreach($datas as $k => $v) : echo "tify['{$k}'] =". \wp_json_encode($v); endforeach; ?>/* ]]> */</script><?php
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

        if ($datas) :
            ?><script type="text/javascript">/* <![CDATA[ */<?php foreach($datas as $k => $v) : echo "tify['{$k}'] =". \wp_json_encode($v); endforeach; ?>/* ]]> */</script><?php
        endif;
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
