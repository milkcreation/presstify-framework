<?php

/**
 * @see https://github.com/kloon/woocommerce-large-sessions
 */

namespace tiFy\Contracts\User;

use tiFy\Contracts\Db\DbFactory;

interface SessionManager
{
    /**
     * Récupération d'une session.
     *
     * @param string $name Nom de qualification de la session.
     *
     * @return null|object|SessionStore
     */
    public function get($name);

    /**
     * Récupération de la base de données
     *
     * @return DbFactory
     *
     * @throws \Exception
     */
    public function getDb();

    /**
     * Déclaration d'une session.
     *
     * @param string $name Nom de qualification de la session.
     * @param array $attrs Attributs de configuration.
     *
     * @return null|SessionStore
     */
    public function register($name, $attrs = []);
}