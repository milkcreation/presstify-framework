<?php

namespace tiFy\Console;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputOption;

class ControllerApplication extends Application
{
    public function setCommands()
    {
        // Déclaration des commandes.
        foreach (config('console.commands', []) as $k => $command) :
            if (is_numeric($k) && class_exists($command)) :
                $command = $this->add(new $command());
            elseif (class_exists($command)) :
                $command = $this->add(new $command($k));
            endif;

            if (!$command->getDefinition()->hasOption('url')) :
                $command->addOption(
                    'url',
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'site url'
                );
            endif;
        endforeach;

        return $this;
    }
}