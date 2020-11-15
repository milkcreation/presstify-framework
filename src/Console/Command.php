<?php declare(strict_types=1);

namespace tiFy\Console;

use Exception;
use Symfony\Component\Console\{
    Command\Command as BaseCommand,
    Input\InputInterface,
    Output\OutputInterface
};
use tiFy\Contracts\Console\Command as CommandContract;
use tiFy\Contracts\Log\Logger as LoggerContract;
use tiFy\Support\MessagesBag;
use tiFy\Support\Proxy\Log;

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
     * Initialisation de la journalisation.
     * @var bool
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
            foreach($messages as $message) {
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
        } elseif (!$this->logger instanceof LoggerContract) {
            $this->logger = Log::registerChannel(
                '[command]' . preg_replace('/\:/', '@', $this->getName()),
                is_array($this->logger) ? $this->logger : []
            );
        }

        if (is_null($level)) {
            return $this->logger ?: null;
        } else {
            $this->logger->log($level, $message, $context);
        }

        return null;
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
        } else {
            return $this->message->add($level, $message, $data, $code);
        }
    }

    /**
     * @inheritDoc
     */
    public function setLogger($logger = true): CommandContract
    {
        $this->logger = ($logger instanceof LoggerContract) ? $logger : !!$logger;

        return $this;
    }
}