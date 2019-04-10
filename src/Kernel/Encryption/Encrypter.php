<?php

namespace tiFy\Kernel\Encryption;

use tiFy\Contracts\Kernel\Encrypter as EncrypterContract;

class Encrypter implements EncrypterContract
{
    /**
     * Clé secrète de hashage.
     * @var string
     */
    private $secret = null;

    /**
     * Clé privée de hashage.
     * @var string
     */
    private $private = null;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $secret Clé secrète de hashage.
     * @param string $private Clé privée de hashage.
     *
     * @return void
     */
    public function __construct($secret = null, $private = null)
    {
        $this->secret = $secret ? : NONCE_KEY;
        $this->private = $private ? : NONCE_SALT;
    }

    /**
     * Traitement de l'action de cryptage ou de decryptage.
     *
     * @param string $value Valeur à traiter.
     * @param string $action Traitement de la valeur à réaliser. encrypt|decrypt. encrypt par défaut.
     * @param string $secret Clé secrète de hashage.
     * @param string $private Clé privée de hashage.
     *
     * @return bool|string
     */
    protected function handle($value, $action = 'encrypt', $secret = null, $private = null)
    {
        $inst = app('encrypter', [$secret, $private]);
        $secret_key = $inst->getSecret();
        $secret_iv = $inst->getPrivate();

        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        switch($action) :
            default :
            case 'encrypt' :
                return base64_encode(openssl_encrypt($value, $encrypt_method, $key, 0, $iv));
                break;
            case 'decrypt' :
                return openssl_decrypt(base64_decode($value), $encrypt_method, $key, 0, $iv);
                break;
        endswitch;
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt($hash, $secret = null, $private = null)
    {
        return $this->handle($hash, 'decrypt', $secret, $private);
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt($plain, $secret = null, $private = null)
    {
        return $this->handle($plain, 'encrypt', $secret, $private);
    }

    /**
     * {@inheritdoc}
     */
    public function generate($length = 12, $special_chars = true, $extra_special_chars = false)
    {
        return wp_generate_password($length, $special_chars, $extra_special_chars);
    }

    /**
     * {@inheritdoc}
     */
    final public function getSecret()
    {
        return $this->secret;
    }

    /**
     * {@inheritdoc}
     */
    final public function getPrivate()
    {
        return $this->private;
    }
}