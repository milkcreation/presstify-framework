<?php

namespace tiFy\Field\Fields\Crypted;

use tiFy\Contracts\Kernel\Encrypter;
use tiFy\Field\FieldController;

class Crypted extends FieldController
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
     * Instance du contrôleur d'encryptage.
     * @var Encrypter
     */
    protected $encrypter;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'init',
            function () {
                add_action(
                    'wp_ajax_tify_field_crypted_encrypt',
                    [$this, 'wp_ajax_encrypt']
                );

                add_action(
                    'wp_ajax_nopriv_tify_field_crypted_encrypt',
                    [$this, 'wp_ajax_encrypt']
                );

                add_action(
                    'wp_ajax_tify_field_crypted_decrypt',
                    [$this, 'wp_ajax_decrypt']
                );

                add_action(
                    'wp_ajax_nopriv_tify_field_crypted_decrypt',
                    [$this, 'wp_ajax_decrypt']
                );

                add_action(
                    'wp_ajax_tify_field_crypted_generate',
                    [$this, 'wp_ajax_generate']
                );

                add_action(
                    'wp_ajax_nopriv_tify_field_crypted_generate',
                    [$this, 'wp_ajax_generate']
                );

                wp_register_style(
                    'FieldCrypted',
                    assets()->url('field/crypted/css/styles.css'),
                    ['dashicons'],
                    180519
                );

                wp_register_script(
                    'FieldCrypted',
                    assets()->url('field/crypted/js/scripts.js'),
                    ['jquery'],
                    180519,
                    true
                );
            }
        );
    }

    /**
     * Récupération du controleur d'encryptage.
     *
     * @return Encrypter
     */
    public function getEncrypter()
    {
        if (is_null($this->encrypter)) :
            $this->encrypter = app('encrypter');
        endif;

        return $this->encrypter;
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('FieldCrypted');
        wp_enqueue_script('FieldCrypted');
    }

    /**
     * Récupération Ajax de la valeur décryptée.
     *
     * @return string
     */
    public function wp_ajax_encrypt()
    {
        $callback = !empty($_REQUEST['encrypt_cb'])
            ? wp_unslash($_REQUEST['encrypt_cb']) :
            "tiFy\\Core\\Control\\CryptedData\\CryptedData::encrypt";

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

        wp_send_json_success($this->getEncrypter()->decrypt(request()->post('cypher')));
    }

    /**
     * Génération Ajax d'une valeur décryptée
     */
    public function wp_ajax_generate()
    {
        $callback = !empty($_REQUEST['generate_cb'])
            ? wp_unslash($_REQUEST['generate_cb'])
            : "tiFy\\Core\\Control\\CryptedData\\CryptedData::generate";

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

        $this->set('attrs.type', $this->get('hide') ? 'password' : 'text');
        $this->set('attrs.size', $this->get('attrs.size') ? : $this->get('length'));

        if(!$this->has('attrs.autocomplete')) :
            $this->set('attrs.autocomplete', 'off');
        endif;

        if($this->get('readonly')):
            $this->push('attrs', 'readonly');
        endif;
        $this->set('attrs.aria-control', 'input');

        $cypher = $this->getValue();
        $this->set('attrs.aria-cypher', $this->getEncrypter()->encrypt($cypher));
        $this->set('attrs.value', $this->get('hide') ? $cypher : $this->set('attrs.value'));
    }
}