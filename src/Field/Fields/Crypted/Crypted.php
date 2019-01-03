<?php

namespace tiFy\Field\Fields\Crypted;

use tiFy\Contracts\Field\Crypted as CryptedContract;
use tiFy\Contracts\Kernel\Encrypter;
use tiFy\Field\FieldController;

class Crypted extends FieldController implements CryptedContract
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Clé d'indice de la valeur de soumission du champ.
     *      @var string $value Valeur courante de soumission du champ.
     *      @var array $attrs Attributs HTML du champ.
     *      @var array $viewer Liste des attributs de configuration du controleur de gabarit d'affichage.
     *      @var array $container Liste des attributs de configuration du conteneur de champ.
     *      @var bool $readonly Controleur en lecture seule (désactive aussi l'enregistrement et le générateur).
     *      @var int $length.
     *      @var bool hide Masquage de la valeur true (masquée)|false (visible en clair)
     * }
     */
    protected $attributes = [
        'before'    => '',
        'after'     => '',
        'name'      => '',
        'value'     => '',
        'attrs'     => [],
        'viewer'    => [],
        'container' => [
            'attrs' => [],
        ],
        'readonly'  => false,
        'length'    => 32,
        'hide'      => true,
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
                    'wp_ajax_tify_field_crypted_decrypt',
                    [$this, 'wp_ajax_decrypt']
                );

                add_action(
                    'wp_ajax_nopriv_tify_field_crypted_decrypt',
                    [$this, 'wp_ajax_decrypt']
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
     * @return void
     */
    public function wp_ajax_decrypt()
    {
        check_ajax_referer('tiFyFieldCrypted');

        wp_send_json_success($this->getEncrypter()->decrypt(request()->post('cypher')));
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
        $this->set('attrs.value', $this->get('hide') ? $cypher : $this->get('attrs.value'));
    }
}