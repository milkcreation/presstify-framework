<?php

namespace tiFy\Console\Commands;

use tiFy\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Sample
 * @package tiFy\Console\Commands
 *
 * @see https://symfony.com/doc/current/console.html
 */
class Sample extends Command
{
    /**
     * Définition du nom de qualification de la commande.
     * @var string
     */
    protected static $defaultName = 'tify:sample';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            // Description de la commande affichée via "php vendor/bin/bee list"
            ->setDescription(__('Test de commande cli.', 'tify'))

            // Description complète de la commande affichée via "php vendor/bin/bee tify:sample --help"
            ->setHelp(__('Cette commande sert de test de démonstration ...', 'tify') );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Affichage de sortie sur plusieurs lignes dans la console (ou ajouter "\n" à la fin de chaque ligne)
        $output->writeln([
            __('Commande de test', 'tify'),
            '================',
            '',
        ]);

        $output->writeln(__('Ça marche !', 'tify'));
    }
}