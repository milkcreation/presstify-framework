<?php declare(strict_types=1);

namespace tiFy\Field\Fields\PasswordJs;

use tiFy\Contracts\Field\{FieldFactory as FieldFactoryContract, PasswordJs as PasswordJsContract};
use tiFy\Contracts\Encryption\Encrypter;
use tiFy\Field\FieldFactory;

class PasswordJs extends FieldFactory implements PasswordJsContract
{
    /**
     * Instance du contrôleur d'encryptage.
     * @var Encrypter
     */
    protected $encrypter;

    /**
     * {@inheritDoc}
     *
     * @return array {
     *      @var array $attrs Attributs HTML du champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $before Contenu placé avant le champ.
     *      @var string $name Clé d'indice de la valeur de soumission du champ.
     *      @var string $value Valeur courante de soumission du champ.
     *      @var array $viewer Liste des attributs de configuration du pilote d'affichage.
     *      @var array $container Liste des attributs de configuration du conteneur de champ.
     *      @var bool $readonly Controleur en lecture seule (désactive aussi l'enregistrement et le générateur).
     *      @var int $length.
     *      @var bool hide Masquage de la valeur true (masquée)|false (visible en clair)
     * }
     */
    public function defaults(): array
    {
        return [
            'attrs'  => [],
            'after'  => '',
            'before' => '',
            'name'   => '',
            'value'  => '',
            'viewer' => [],
            'container' => [
                'attrs' => [],
            ],
            'hide'      => true,
            'length'    => 32,
            'readonly'  => false
        ];
    }

    /**
     * Récupération du controleur d'encryptage.
     *
     * @return Encrypter
     */
    public function getEncrypter()
    {
        if (is_null($this->encrypter)) {
            $this->encrypter = app('encrypter');
        }

        return $this->encrypter;
    }

    /**
     * @inheritDoc
     */
    public function parse(): FieldFactoryContract
    {
        parent::parse();

        $this->set('container.attrs.id', 'tiFyField-CryptedContainer--' . $this->getId());
        $this->set('container.attrs.data-control', 'password-js');
        $this->set('container.attrs.aria-hide', $this->get('hide') ? 'true' : 'false');
        $this->set('container.attrs.data-options', [
            '_ajax_nonce' => wp_create_nonce('tiFyFieldCrypted')
        ]);

        $this->set('attrs.type', $this->get('hide') ? 'password' : 'text');
        $this->set('attrs.size', $this->get('attrs.size') ? : $this->get('length'));

        if(!$this->has('attrs.autocomplete')) {
            $this->set('attrs.autocomplete', 'off');
        }

        if($this->get('readonly')) {
            $this->push('attrs', 'readonly');
        }
        $this->set('attrs.data-control', 'password-js.input');

        $cypher = $this->getValue();
        $this->set('attrs.data-cypher', $this->getEncrypter()->encrypt($cypher));
        $this->set('attrs.value', $this->get('hide') ? $cypher : $this->get('attrs.value'));

        return $this;
    }

    /**
     * Récupération Ajax de la valeur décryptée.
     *
     * @return array
     */
    public function xhrDecrypt(): array
    {
        return [
            'success' => true,
            'data'    => $this->getEncrypter()->decrypt(request()->input('cypher'))
        ];
    }
}