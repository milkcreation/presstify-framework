<?php

declare(strict_types=1);

namespace tiFy\Contracts\Console;

use Pollen\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Output\OutputInterface;

use tiFy\Support\MessagesBag;

/**
 * USAGE :
 * Liste des commandes disponibles
 * -------------------------------
 * php console list
 *
 * TIPS :
 * Arrêt complet des commandes CLI lancées
 * ---------------------------------------
 * pkill -9 php
 */

/**
 * @mixin BaseCommand
 */
interface Command
{
    /**
     * Traitement des messages de notification.
     *
     * @param OutputInterface $output
     * @param bool $forget Suppression des messages
     *
     * @return void
     */
    public function handleNotices(OutputInterface $output, bool $forget = true): void;

    /**
     * Journalisation.
     *
     * @param mixed  $level
     * @param string $message
     * @param array $context
     *
     * @return string|LoggerInterface|null
     */
    public function log($level = null, string $message = '', array $context = []);

    /**
     * Ajout d'un message ou récupération de l'instance du gestionnaire de message.
     *
     * @param string|int|null $level
     * @param string|null $message
     * @param array|null $data
     * @param string|null $code
     *
     * @return MessagesBag|string|null
     */
    public function message($level = null, string $message = null, ?array $data = [], ?string $code = null);

    /**
     * Définition de la journalisation.
     *
     * @param LoggerInterface|bool $logger
     *
     * @return static
     */
    public function setLogger($logger = true): Command;
}