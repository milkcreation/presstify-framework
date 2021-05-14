<?php

declare(strict_types=1);

namespace tiFy\Console;

use Exception;
use Pollen\Log\LogManager;
use Pollen\Log\LogManagerInterface;
use Pollen\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use tiFy\Contracts\Console\Command as CommandContract;
use tiFy\Support\MessagesBag;
use RuntimeException;

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
class Command extends BaseCommand implements CommandContract
{
    /**
     * @var LogManagerInterface
     */
    protected $logManager;

    /**
     * Initialisation de la journalisation.
     * @var LoggerInterface|bool
     */
    protected $logger = true;

    /**
     * Données d'enregistrement de l'élément.
     * @var MessagesBag
     */
    protected $message;

    /**
     * @inheritDoc
     */
    protected function configure() { }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function handleNotices(OutputInterface $output, bool $forget = true): void
    {
        foreach ($this->message()->all() as $level => $messages) {
            foreach ($messages as $message) {
                $this->log($level, $message);
            }

            $output->writeln($messages);
        }

        if ($forget === true) {
            $this->message()->flush();
        }
    }

    /**
     * @inheritDoc
     */
    public function log($level = null, string $message = '', array $context = [])
    {
        if ($this->logger === false) {
            return null;
        }

        if (!$this->logger instanceof LoggerInterface) {
            $this->logger = $this->logManager()->registerChannel(
                '[command]' . preg_replace('/\:/', '@', $this->getName()),
                is_array($this->logger) ? $this->logger : []
            );
        }

        if (is_null($level)) {
            return $this->logger ?: null;
        }
        $this->logger->log($level, $message, $context);

        return null;
    }

    protected function logManager(): LogManagerInterface
    {
        if ($this->logManager === null) {
            try {
                $this->logManager = LogManager::getInstance();
            } catch (RuntimeException $e) {
                $this->logManager = new LogManager();
            }
        }

        return $this->logManager;
    }

    /**
     * @inheritDoc
     */
    public function message($level = null, string $message = null, ?array $data = [], ?string $code = null)
    {
        if (is_null($this->message)) {
            $this->message = new MessagesBag();
        }

        if (is_null($level)) {
            return $this->message;
        }
        return $this->message->add($level, $message, $data, $code);
    }

    /**
     * @inheritDoc
     */
    public function setLogger($logger = true): CommandContract
    {
        $this->logger = ($logger instanceof LoggerInterface) ? $logger : (bool)$logger;

        return $this;
    }
}