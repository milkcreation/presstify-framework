<?php

namespace tiFy\Contracts\Cron;

use \DateTime;
use tiFy\Contracts\Kernel\ParametersBagIteratorInterface;

interface CronJobInterface extends ParametersBagIteratorInterface
{
    /**
     * Lancement de la commande à executer.
     *
     * @return void
     */
    public function exec();

    /**
     * Récupération des variables passées en arguments.
     *
     * @return array
     */
    public function getArgs();

    /**
     * Récupération de la commande à executer.
     *
     * @return false|callable
     */
    public function getCommand();

    /**
     * Récupération de la date d'exécution de la première itération.
     *
     * @return DateTime
     */
    public function getDate();

    /**
     * Récupération d'une instance du gestionnaire de date.
     * {@internal La timezone correspond aux réglages de l'application.}
     *
     * @param string $time Date
     *
     * @return DateTime
     */
    public function getDatetime($time = 'now');

    /**
     * Récupération de la description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Récupération de la fréquence d'exécution des itérations.
     *
     * @return string
     */
    public function getFrequency();

    /**
     * Récupération de l'accroche de l'action de déclenchement
     *
     * @return string
     */
    public function getHook();

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de la date de la prochaine exécution de la tâche.
     *
     * @return DateTime
     */
    public function getNext();

    /**
     * Récupération de l'horodatage d'exécution de la première itération.
     *
     * @return int
     */
    public function getTimestamp();

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Vérification de l'activité du mode test.
     *
     * @return boolean
     */
    public function onTest();
}