<?php

namespace tiFy\Components\Tools\Cryptor;

class Cryptor
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
        if ($secret) :
            $this->secret = $secret;
        else :
            $this->secret = NONCE_KEY;
        endif;

        if ($private) :
            $this->private = $private;
        else :
            $this->private = NONCE_SALT;
        endif;
    }

    /**
     * Récupération de la clée secrète de hashage.
     *
     * @return string
     */
    final public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Récupération de la clé privée de hashage.
     *
     * @return string
     */
    final public function getPrivate()
    {
        return $this->private;
    }

    /**
     * Encryptage.
     *
     * @param string $plain Texte à encrypter.
     * @param string $secret Clé secrète de hashage.
     * @param string $private Clé privée de hashage.
     *
     *
     * @return string
     */
    final public static function encrypt($plain, $secret = null, $private = null)
    {
        return self::crypt($plain, 'encrypt', $secret, $private);
    }

    /**
     * Décryptage.
     *
     * @param string $hash Texte à décrypter.
     * @param string $secret Clé secrète de hashage.
     * @param string $private Clé privée de hashage.
     *
     * @return string
     */
    final public static function decrypt($hash, $secret = null, $private = null)
    {
        return self::crypt($hash, 'decrypt', $secret, $private);
    }

    /**
     * Lancement de l'action de cryptage ou de decryptage.
     *
     * @param string $value Valeur à traiter.
     * @param string $action Traitement de la valeur à réaliser. encrypt|decrypt. encrypt par défaut.
     * @param string $secret Clé secrète de hashage.
     * @param string $private Clé privée de hashage.
     *
     * @return bool|string
     */
    final private static function crypt($value, $action = 'encrypt', $secret = null, $private = null)
    {
        $inst = new self($secret, $private);
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
}