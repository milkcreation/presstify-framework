<?php

namespace tiFy\Contracts\Kernel;

use Monolog\Logger as MonologLogger;
use Monolog\ResettableInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

/**
 * Interface Logger
 *
 * @package tiFy\Contracts\Kernel
 *
 * @mixin MonologLogger
 */
interface Logger extends PsrLoggerInterface, ResettableInterface
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
     *
     * @return self
     */
    public static function create($name = 'system', $attrs = []);

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