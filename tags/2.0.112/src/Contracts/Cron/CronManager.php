<?php

namespace tiFy\Contracts\Cron;

use tiFy\Contracts\Kernel\Collection;

interface CronManager extends Collection
{
    /**
     * Suppression d'une tâche planifiée selon son identifiant d'action.
     *
     * @param string $hook Identifiant de qualification de l'action.
     *
     * @return static
     */
    public function clear($hook);

    /**
     * Récupération d'une tâche planifiée déclarée.
     *
     * @param string $name Nom de qualification de l'élément.
     *
     * @return null|CronJob
     */
    public function getItem($name);

    /**
     * Enregistrement d'une tâche planifiée.
     *
     * @param string $name Identifiant de qualification.
     * @param array $attrs Liste des attribut de configuration.
     *
     * @return null|CronJob
     */
    public function register($name, $attrs = []);

    /**
     * Définition d'une tâche planifiée.
     *
     * @param string $name Identifiant de qualification.
     * @param CronJob $job Instance de la tâche à définir.
     *
     * @return static
     */
    public function set($name, CronJob $job);
}