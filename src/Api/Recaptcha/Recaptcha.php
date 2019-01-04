<?php

/**
 * @see https://github.com/google/recaptcha
 */

namespace tiFy\Api\Recaptcha;

use ReCaptcha\ReCaptcha as ReCaptchaSdk;
use ReCaptcha\RequestMethod\SocketPost;
use tiFy\Api\Recaptcha\Field\Recaptcha as RecaptchaField;
use tiFy\Contracts\Api\Recaptcha as RecaptchaInterface;
use tiFy\Field\FieldManager;

class Recaptcha extends ReCaptchaSdk implements RecaptchaInterface
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $secretkey Clé secrète, requise pour la communication entre le site et Google. Veillez à ne surtout pas divulger cette clé !
     *      @var string $sitekey Clé du site, utilisée dans le code HTML
     * }
     */
    protected $attributes = [];

    /**
     * Liste des widgets déclarés.
     * @var array
     */
    protected $widgets = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    protected function __construct($attrs = [])
    {
        try {
            parent::__construct($attrs['secretkey'], (ini_get('allow_url_fopen') ? null : new SocketPost));
            $this->attributes = $attrs;

            add_action(
                'after_setup_theme',
                function() {
                    /** @var FieldManager $field */
                    $field = app('field');

                    $field->register('recaptcha', RecaptchaField::class);
                }
            );

            add_action(
                'wp_footer',
                function () {
                    if ($this->widgets) :
                    ?><script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo $this->getLanguage(); ?>&onload=onloadCallback&render=explicit" async defer></script><?php
                    endif;
                }
            );

            add_action(
                'wp_footer',
                function () {
                    if ($this->widgets) :
                        $js = "var onloadCallback=function(){";
                        foreach($this->widgets as $id => $params) :
                            $js .= "grecaptcha.render('{$id}', " . json_encode($params) . ");";
                        endforeach;
                        $js .= '};';

                        assets()->addInlineJs($js, 'user', true);
                    endif;
                },
                1
            );
        } catch (\RuntimeException $e) {
            wp_die($e->getMessage(), __('Api reCaptcha : Erreur de configuration', 'tify'), $e->getCode());
        }
    }

    /**
     * Court-circuitage de l'instanciation.
     *
     * @return void
     */
    private function __clone()
    {

    }

    /**
     * Court-circuitage de l'instanciation.
     *
     * @return void
     */
    private function __wakeup()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function addWidgetRender($id, $params = [])
    {
        $this->widgets[$id] = $params;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function create($attrs = [])
    {
        return new static(
            array_merge(
                [
                    'secretkey' => '',
                    'sitekey'   => '',
                ],
                $attrs
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguage()
    {
        global $locale;

        switch ($locale) :
            default :
                list($lang, $indice) = preg_split('/_/', $locale, 2);
                break;
            case 'zh_CN':
                $lang = 'zh-CN';
                break;
            case 'zh_TW':
                $lang = 'zh-TW';
                break;
            case 'en_GB' :
                $lang = 'en-GB';
                break;
            case 'fr_CA' :
                $lang = 'fr-CA';
                break;
            case 'de_AT' :
                $lang = 'de-AT';
                break;
            case 'de_CH' :
                $lang = 'de-CH';
                break;
            case 'pt_BR' :
                $lang = 'pt-BR';
                break;
            case 'pt_PT' :
                $lang = 'pt-PT';
                break;
            case 'es_AR' :
            case 'es_CL' :
            case 'es_CO' :
            case 'es_MX' :
            case 'es_PE' :
            case 'es_PR' :
            case 'es_VE' :
                $lang = 'es-419';
                break;
        endswitch;

        return $lang;
    }

    /**
     * {@inheritdoc}
     */
    public function getSiteKey()
    {
        return $this->attributes['sitekey'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function validation()
    {
        return $this->verify(request()->post('g-recaptcha-response'), request()->server('REMOTE_ADDR'));
    }
}