<?php

namespace tiFy\Kernel\Assets;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use tiFy\Contracts\Kernel\Assets as AssetsContract;

final class Assets implements AssetsContract
{
    /**
     * Liste des attributs JS.
     *
     * @var array
     */
    protected $dataJs = [];

    /**
     * Liste des styles css.
     *
     * @var array
     */
    protected $inlineCss = [];

    /**
     * Liste des styles css.
     *
     * @var array
     */
    protected $inlineJs = [];

    /**
     * Liste des librairies tierces CSS +JS
     *
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
        $this->thirdParty = require_once(__DIR__ . '/Resources/config/third-party.php');

        $this->setDataJs('ajax_url', admin_url('admin-ajax.php', 'relative'), 'both', false);
        $this->setDataJs('ajax_response', [], 'both', false);

        add_action(
            'admin_head',
            function () {
                if ($css = Arr::get($this->inlineCss, 'admin', '')) :
                    ?>
                    <style type="text/css"><?php echo $css; ?></style><?php
                endif;

                $datas = (new Collection(Arr::get($this->dataJs, 'admin', [])))
                    ->where('in_footer', '===', false)
                    ->pluck('value', 'key')
                    ->all();

                $js = Arr::get($this->inlineJs, 'admin.header', '')

                ?>
                <script type="text/javascript">/* <![CDATA[ */var tify_ajaxurl = '<?php echo admin_url('admin-ajax.php', 'relative');?>';<?php echo 'var tify={};'; if ($datas) : foreach ($datas as $k => $v) : echo "tify['{$k}']=" . \wp_json_encode($v) . ";"; endforeach; endif; echo $js; ?>/* ]]> */</script><?php
            }
        );

        add_action(
            'admin_footer',
            function () {
                $datas = (new Collection(Arr::get($this->dataJs, 'admin', [])))
                    ->where('in_footer', '===', true)
                    ->pluck('value', 'key')
                    ->all();

                $js = Arr::get($this->inlineJs, 'admin.footer', '');

                if ($datas || $js) :
                    ?>
                    <script type="text/javascript">/* <![CDATA[ */<?php if ($datas) : foreach ($datas as $k => $v) : echo "tify['{$k}']=" . \wp_json_encode($v) . ";"; endforeach; endif; echo $js; ?>/* ]]> */</script><?php
                endif;
            }
        );

        add_action(
            'init',
            function () {
                wp_register_style('tiFyAdmin', $this->url('/admin/css/styles.css'), [], 180528);
                wp_register_script('tiFyAdmin', $this->url('/admin/js/scripts.js'), ['jquery'], 180528, true);

                foreach (Arr::get($this->thirdParty, 'css', []) as $handle => $attrs) :
                    wp_register_style($handle, $attrs[0], $attrs[1], $attrs[2], $attrs[3]);
                endforeach;

                foreach (Arr::get($this->thirdParty, 'js', []) as $handle => $attrs) :
                    wp_register_script($handle, $attrs[0], $attrs[1], $attrs[2], $attrs[3]);
                endforeach;
            }
        );

        add_action(
            'wp_footer',
            function () {
                $datas = (new Collection(Arr::get($this->dataJs, 'user', [])))
                    ->where('in_footer', '===', true)
                    ->pluck('value', 'key')
                    ->all();

                $js = Arr::get($this->inlineJs, 'user.footer', '');

                if ($datas || $js) :
                    ?><script type="text/javascript">/* <![CDATA[ */<?php if ($datas) : foreach ($datas as $k => $v) : echo "tify['{$k}']=" . wp_json_encode($v) . ";"; endforeach; endif; echo $js; ?>/* ]]> */</script><?php
                endif;
            }
        );

        add_action(
            'wp_head',
            function () {
                if ($css = Arr::get($this->inlineCss, 'user', '')) :
                    ?>
                    <style type="text/css"><?php echo $css; ?></style><?php
                endif;

                $datas = (new Collection(Arr::get($this->dataJs, 'user', [])))
                    ->where('in_footer', '===', false)
                    ->pluck('value', 'key')
                    ->all();

                $js = Arr::get($this->inlineJs, 'user.header', '');

                ?>
                <script type="text/javascript">/* <![CDATA[ */var tify_ajaxurl = '<?php echo admin_url('admin-ajax.php', 'relative');?>';<?php echo 'var tify={};'; if ($datas) : foreach ($datas as $k => $v) : echo "tify['{$k}']=" . \wp_json_encode($v) . ";"; endforeach; endif; echo $js; ?>/* ]]> */</script><?php
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDataJs($key, $value, $context = ['admin', 'user'], $in_footer = true)
    {
        if (is_string($context)) :
            $context = (array)$context;
        endif;

        $context = in_array('both', $context) ? ['admin', 'user'] : $context;

        if (is_array($value)) :
            foreach ($value as $k => &$v) :
                if ( ! is_scalar($v)) :
                    continue;
                endif;

                $v = html_entity_decode((string)$v, ENT_QUOTES, 'UTF-8');
            endforeach;
        elseif (is_scalar($value)) :
            $value = html_entity_decode((string)$value, ENT_QUOTES, 'UTF-8');
        endif;

        foreach ($context as $ui) :
            Arr::set($this->dataJs, "{$ui}.{$key}", compact('in_footer', 'key', 'value'));
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function url($path = '')
    {
        return home_url('vendor/presstify/framework/assets' . ($path ? '/' . ltrim($path, '/') : $path));
    }

    /**
     * {@inheritdoc}
     */
    public function addInlineCss($css, $ui = 'user')
    {
        switch ($ui) :
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
     * {@inheritdoc}
     */
    public function addInlineJs($js, $ui = 'user', $footer = false)
    {
        $place = $footer ? '.footer' : '.header';

        switch ($ui) :
            case 'admin' :
            case 'user' :
                Arr::set($this->inlineJs, $ui . $place, Arr::get($this->inlineJs, $ui . $place, '') . (string)$js);
                break;
            case 'both' :
                Arr::set($this->inlineJs, "admin{$place}",
                    Arr::get($this->inlineJs, "admin{$place}", '') . (string)$js);
                Arr::set($this->inlineJs, "user{$place}", Arr::get($this->inlineJs, "user{$place}", '') . (string)$js);
                break;
        endswitch;
    }

    /**
     * @todo
     */
    public function setAjaxResponse($key, $value, $context = ['admin', 'user'])
    {
        $this->setDataJs("ajaxResponse.{$key}", $value, $context, true);
    }
}
