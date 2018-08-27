<?php

namespace tiFy\Components\Form\FieldTypes\Recaptcha;

use ReCaptcha\ReCaptcha as ReCaptchaLib;
use ReCaptcha\RequestMethod\SocketPost;
use tiFy\Api\Api;
use tiFy\Form\Fields\AbstractFieldTypeController;
use tiFy\Form\Fields\FieldItemController;

class Recaptcha extends AbstractFieldTypeController
{
    /**
     * Liste des propriétés supportées.
     * @var array
     */
    protected $support = [
        'label',
        'request',
        'wrapper',
    ];

    /**
     * Compteur d'instance.
     * @var int
     */
    protected static $instanceCount = 0;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->defaultOptions = [
            'sitekey'   => false,
            'secretkey' => false,
            'lang'      => $this->getLanguage(),
            'theme'     => 'light',
        ];

        $this->callbacks = [
            'handle_check_field' => [$this, 'cb_handle_check_field'],
        ];
    }

    /**
     * Court-circuitage de contrôle d'intégrité des champs.
     *
     * @param array $errors Liste des erreurs de soumission de formulaire.
     * @param FieldItemController $field Classe de rappel du controleur de champ.
     *
     * @return void
     */
    public function cb_handle_check_field(&$errors, $field)
    {
        if ($field->get('type') !== 'recaptcha') :
            return;
        endif;

        if ($recaptcha = $this->appServiceGet(Api::class)->get('recaptcha')) :
        else :
            $options = $this->getOptions();

            try {
                $recaptcha = new ReCaptchaLib(
                    $options['secretkey'],
                    ini_get('allow_url_fopen') ? null : new SocketPost()
                );
            } catch (\RuntimeException $e) {
                wp_die($e->getMessage(), __('Erreur de configuration du champ reCaptcha', 'tify'), $e->getCode());
            }
        endif;

        $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

        if (!$resp->isSuccess()) :
            $errors[] = [
                'message' => __('La saisie de la protection antispam est incorrecte.', 'tify'),
                'type'    => 'field',
                'slug'    => $field->getSlug(),
                'order'   => $field->getOrder(),
            ];
        endif;
    }

    /**
     * Affichage du champ.
     *
     * @return string
     */
    public function render()
    {
        $ID = preg_replace('/-/', '_', sanitize_key($this->getForm()->getName()));

        // Définition des options
        $options = $this->getOptions();
        if ($recaptcha = $this->appServiceGet(Api::class)->get('recaptcha')) :
            $options['sitekey'] = $recaptcha->getSiteKey();
        endif;
        $options['tabindex'] = $this->relField()->getTabIndex();

        $instance = $this;

        // Chargement des scripts dans le pied de page
        if (!self::$instanceCount++) :
            add_action(
                'wp_footer',
                function () use ($ID, $instance) {
?><script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo $instance->getLanguage(); ?>&onload=onloadCallback_<?php echo $ID; ?>&render=explicit" async defer></script><?php
                }
            );
        endif;

        add_action(
            'wp_footer',
            function () use ($ID, $options) {
?><script type="text/javascript">/* <![CDATA[ */var onloadCallback_<?php echo $ID;?>=function(){grecaptcha.render('g-recaptcha-<?php echo $ID;?>',<?php echo json_encode($options);?>);};/* ]]> */</script><?php
            }
        );

        // Affichage du champ ReCaptcha
        $output = "";
        $output .= "<input type=\"hidden\" name=\"" . esc_attr($this->relField()->getName()) . "\" value=\"-1\">";
        $output .= "<div id=\"g-recaptcha-{$ID}\" class=\"g-recaptcha\" data-sitekey=\"{$options['sitekey']}\" data-theme=\"{$options['theme']}\" data-tabindex=\"{$options['tabindex']}\"></div>";

        return $output;
    }

    /**
     * Récupération de la langue.
     *
     * @return string
     */
    private function getLanguage()
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
}