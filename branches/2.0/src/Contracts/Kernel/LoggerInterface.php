<?php

namespace tiFy\Contracts\Kernel;

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use tiFy\Contracts\App\AppInterface;

interface LoggerInterface extends PsrLoggerInterface
{
    /**
     * Alias de création d'un message de notification.
     *
     * @param string $message Intitulé du message.
     * @param array $context Liste des données de contexte.
     *
     * @return boolean
     */
    public function addSuccess($message, array $context = []);

    /**
     * Déclaration d'un controleur de journalisation.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param AppInterface $app Instance de l'application.
     *
     * @return self
     */
    public static function create($name = 'system', $attrs = [], AppInterface $app);

    /**
     * Traitement de la liste des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function parse($attrs = []);

    /**
     * Alias de création d'un message de notification.
     *
     * @param string $message Intitulé du message.
     * @param array $context Liste des données de contexte.
     *
     * @return boolean
     */
    public function success($message, array $context = []);
}