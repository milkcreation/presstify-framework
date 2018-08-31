<?php

namespace tiFy\Components\Field\Crypted;

use tiFy\Field\AbstractFieldItem;
use tiFy\Kernel\Tools;

class Crypted extends AbstractFieldItem
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var array $container Liste des attributs de configuration du conteneur de champ.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var string $value Attribut de configuration de la valeur de soumission du champ "value" si l'élément est selectionné.
     *      @var array $attrs Liste des propriétés de la balise HTML.
     *      @var bool $readonly Controleur en lecture seule (désactive aussi l'enregistrement et le générateur).
     *      @var int $length.
     *      @var bool hide Masquage de la valeur true (masquée)|false (visible en clair)
     * }
     */
    protected $attributes = [
        'container'     => [
            'attrs' => []
        ],
        'name'        => '',
        'value'       => '',
        'attrs'       => [],
        'readonly'    => false,
        'length'      => 32,
        'hide'        => true
    ];

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        // Actions ajax
        $this->appAddAction(
            'wp_ajax_tify_field_crypted_encrypt',
            'wp_ajax_encrypt'
        );
        $this->appAddAction(
            'wp_ajax_nopriv_tify_field_crypted_encrypt',
            'wp_ajax_encrypt'
        );
        $this->appAddAction(
            'wp_ajax_tify_field_crypted_decrypt',
            'wp_ajax_decrypt'
        );
        $this->appAddAction(
            'wp_ajax_nopriv_tify_field_crypted_decrypt',
            'wp_ajax_decrypt'
        );
        $this->appAddAction(
            'wp_ajax_tify_field_crypted_generate',
            'wp_ajax_generate'
        );
        $this->appAddAction(
            'wp_ajax_nopriv_tify_field_crypted_generate',
            'wp_ajax_generate'
        );

        \wp_register_style(
            'FieldCrypted',
            \assets()->url('/field/crypted/css/styles.css'),
            ['dashicons'],
            180519
        );
        \wp_register_script(
            'FieldCrypted',
            \assets()->url('/field/crypted/js/scripts.js'),
            ['jquery'],
            180519,
            true
        );
    }

    /**
     * Mise en file des scripts.
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        \wp_enqueue_style('FieldCrypted');
        \wp_enqueue_script('FieldCrypted');
    }

    /**
     * Récupération Ajax de la valeur décryptée.
     *
     * @return string
     */
    public function wp_ajax_encrypt()
    {
        $callback = !empty($_REQUEST['encrypt_cb']) ? wp_unslash($_REQUEST['encrypt_cb']) : "tiFy\\Core\\Control\\CryptedData\\CryptedData::encrypt";
        $response = call_user_func($callback, $_REQUEST['value'], $_REQUEST['data']);

        if (is_wp_error($response)) :
            wp_send_json_error($response->get_error_message());
        else :
            wp_send_json_success($response);
        endif;
    }

    /**
     * Récupération Ajax de la valeur décryptée.
     *
     * @return string
     */
    public function wp_ajax_decrypt()
    {
        check_ajax_referer('tiFyFieldCrypted');

        \wp_send_json_success(Tools::Cryptor()->decrypt($this->appRequest('POST')->get('cypher')));
    }

    /**
     * Génération Ajax d'une valeur décryptée
     */
    public function wp_ajax_generate()
    {
        $callback = !empty($_REQUEST['generate_cb']) ? wp_unslash($_REQUEST['generate_cb']) : "tiFy\\Core\\Control\\CryptedData\\CryptedData::generate";
        $response = call_user_func($callback, $_REQUEST['data']);

        if (is_wp_error($response)) :
            wp_send_json_error($response->get_error_message());
        else :
            wp_send_json_success($response);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('container.attrs.id', 'tiFyField-CryptedContainer--' . $this->getId());
        $this->set('container.attrs.aria-control', 'crypted');
        $this->set('container.attrs.aria-hide', $this->get('hide') ? 'true' : 'false');
        $this->set(
            'container.attrs.data-options',
            [
                '_ajax_nonce' => wp_create_nonce('tiFyFieldCrypted')
            ]
        );

        $this->setAttr('type', $this->get('hide') ? 'password' : 'text');
        $this->setAttr('size', $this->getAttr('size') ? : $this->get('length'));

        if(!$this->hasAttr('autocomplete')) :
            $this->setAttr('autocomplete', 'off');
        endif;

        if($this->get('readonly')):
            $this->setAttr('readonly');
        endif;
        $this->setAttr('aria-control', 'input');

        $cypher = $this->getAttr('value');
        $this->setAttr('aria-cypher', Tools::Cryptor()->encrypt($cypher));
        $this->setAttr('value', $this->get('hide') ? $cypher : $this->getAttr('value'));
    }
}