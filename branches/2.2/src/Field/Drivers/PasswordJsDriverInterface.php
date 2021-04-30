<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers;

use tiFy\Contracts\Encryption\Encrypter;
use tiFy\Field\FieldDriverInterface;

interface PasswordJsDriverInterface extends FieldDriverInterface
{
    /**
     * Récupération du controleur d'encryptage.
     *
     * @return Encrypter
     */
    public function getEncrypter(): Encrypter;

    /**
     * Contrôleur de traitement de la requête XHR.
     *
     * @param array ...$args Liste dynamique de variables passés en argument dans l'url de requête
     *
     * @return array
     */
    public function xhrResponse(...$args): array;
}