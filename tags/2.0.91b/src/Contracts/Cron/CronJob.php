<?php

namespace tiFy\Contracts\Cron;

use Carbon\Carbon;
use tiFy\Contracts\Kernel\Logger;
use tiFy\Contracts\Kernel\ParamsBag;

interface CronJob extends ParamsBag
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
     * @return Carbon
     */
    public function getDate();

    /**
     * Récupération d'une instance du gestionnaire de date.
     * {@internal La timezone correspond aux réglages de l'application.}
     *
     * @param string $time Date
     *
     * @return Carbon
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
     * Récupération d'une information stockée en base.
     *
     * @param string $key Indice de qualification.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getInfo($key, $default = null);

    /**
     * Récupération de la date de la dernière exécution de la tâche.
     *
     * @return Carbon
     */
    public function getLastDate();

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de la date de la prochaine exécution de la tâche.
     *
     * @return Carbon
     */
    public function getNextDate();

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
     * Récupération de l'instance du controleur de journalisation.
     *
     * @return Logger
     */
    public function log();

    /**
     * Vérification de l'activité du mode test.
     *
     * @return boolean
     */
    public function onTest();

    /**
     * Mise à jour d'une information stockée en base.
     *
     * @param string $key Indice de qualification.
     * @param mixed $value Valeur de l'info.
     *
     * @return $this
     */
    public function updateInfo($key, $value);
}