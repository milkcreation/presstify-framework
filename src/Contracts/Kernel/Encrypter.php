<?php

namespace tiFy\Contracts\Kernel;

interface Encrypter
{
    /**
     * Décryptage.
     *
     * @param string $hash Texte à décrypter.
     * @param string $secret Clé secrète de hashage.
     * @param string $private Clé privée de hashage.
     *
     * @return string
     */
    public function decrypt($hash, $secret = null, $private = null);

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
    public function encrypt($plain, $secret = null, $private = null);

    /**
     * Generation.
     *
     * @param int $length Longueur de la chaine.
     * @param bool $special_chars Activation des caractère spéciaux. !|@|#|$|%|^|&|*|(|)|.
     * @param bool $extra_special_chars Activation des caractère spéciaux complémentaires. -|_| |[|]|{|}|<|>|~|`|+|=|,|.|;|:|/|?|||.
     *
     * @return string
     */
    public function generate($length = 12, $special_chars = true, $extra_special_chars = false);

    /**
     * Récupération de la clée secrète de hashage.
     *
     * @return string
     */
    public function getSecret();

    /**
     * Récupération de la clé privée de hashage.
     *
     * @return string
     */
    public function getPrivate();
}