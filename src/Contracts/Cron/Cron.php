<?php

namespace tiFy\Contracts\Cron;

interface Cron
{
    /**
     * Déclaration d'une tâche planifiée.
     *
     * @param string $name Identifiant de qualification.
     * @param array $attrs Liste des attribut de configuration.
     *
     * @return $this
     */
    public function add($name, $attrs);

    /**
     * Récupération de la liste des tâches planifiées déclarées.
     *
     * @return CronJobInterface[]
     */
    public function all();

    /**
     * Suppression d'une tâche planifiée selon son identifiant d'action.
     *
     * @param string $hook Identifiant de qualification de l'action.
     *
     * @return $this
     */
    public function clear($hook);

    /**
     * Récupération d'une tâche planifiée déclarée.
     *
     * @param string $name Nom de qualification de l'élément.
     *
     * @return null|CronJobInterface
     */
    public function get($name);
}