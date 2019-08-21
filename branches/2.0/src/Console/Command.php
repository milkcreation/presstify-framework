<?php declare(strict_types=1);

namespace tiFy\Console;

use Exception;
use Symfony\Component\Console\{
    Command\Command as BaseCommand,
    Input\InputInterface,
    Output\OutputInterface
};

class Command extends BaseCommand
{
    /**
     * @inheritdoc
     */
    protected function configure() { }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output) { }
}