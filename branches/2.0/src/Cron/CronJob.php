<?php

namespace tiFy\Cron;

use \DateTime;
use \DateTimeZone;
use Carbon\Carbon;
use tiFy\Contracts\Cron\CronJob as CronJobContract;
use tiFy\Contracts\Kernel\Logger;
use tiFy\Kernel\Params\ParamsBag;

class CronJob extends ParamsBag implements CronJobContract
{
    /**
     * Liste des attributs de configuration.
     * @var array {
     *      @var string $title Intitulé de qualification.
     *      @var string $description Description.
     *      @var int|string|Carbon $date Date de déclenchement de la première itération.
     *      @var string $freq Fréquence d'exécution des itérations.
     *      @var callable $command
     *      @var array $args Liste des variables complémentaires passées en arguments.
     *      @var boolean|array $log Liste des attributs de configuration de la journalisation.
     * }
     */
    protected $attributes = [];

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification de la tâche.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        $this->name = $name;

        parent::__construct($attrs);

        add_action(
            $this->getHook(),
            $this
        );
    }

    /**
     * Execution d'une instance de la classe.
     *
     * @return void
     */
    final public function __invoke()
    {
        if (wp_doing_cron() || $this->onTest()) :
            $start = $this->getDatetime()->setTimestamp(time());

            set_time_limit(0);

            is_callable($this->getCommand())
                ? call_user_func_array($this->getCommand(), [$this->getArgs(), $this])
                : $this->exec();

            $end = $this->getDatetime()->setTimestamp(time());

            $this->updateInfo('last', $end->getTimestamp());

            $this->log()->notice(
                sprintf(
                    __('La tâche "%s" démarrée le %s s\'est terminée le %s'),
                    $this->getName(),
                    $start->format('d/m/Y à H:i:s'),
                    $end->format('d/m/Y à H:i:s')
                )
            );
            exit;
        elseif(!$this->onTest()) :
            wp_die(
                sprintf(
                    __(
                        '<h3>La mode TEST doit être actif</h3>' .
                        '<p>Pour afficher le résultat depuis un navigateur, ' .
                        'activer le mode test pour la tâche <em>%s</em>.</p>' .
                        '<b>Attention, veillez à desactiver le mode test de vos tâches en production.</b>',
                        'tify'
                    ),
                    $this->getName()
                ),
                __('Mode test inactif', 'tify'),
                500
            );
        endif;
    }

    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return [
            'hook'          => 'cron.' . $this->getName(),
            'title'         => $this->getName(),
            'description'   => '',
            'date'          => date('Y-m-d H:i:s', mktime(0, 0, 0, 01, 01, 1971)),
            'freq'          => 'daily',
            'command'       => [$this, 'exec'],
            'args'          => [],
            'log'           => true,
            'test'          => false
        ];
    }

    /**
     * @inheritdoc
     */
    public function exec()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getArgs()
    {
        return $this->get('args', []);
    }

    /**
     * @inheritdoc
     */
    public function getCommand()
    {
        return $this->get('command');
    }

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->get('date');
    }

    /**
     * @inheritdoc
     */
    public function getDatetime($time = 'now')
    {
        return new Carbon($time, new DateTimeZone(get_option('timezone_string')));
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @inheritdoc
     */
    public function getFrequency()
    {
        return $this->get('freq');
    }

    /**
     * @inheritdoc
     */
    public function getHook()
    {
        return $this->get('hook');
    }

    /**
     * @inheritdoc
     */
    public function getInfo($key, $default = null)
    {
        $infos = get_option('cron_job_infos', []);
        return (isset($infos[$this->getHook()][$key]))
            ? $infos[$this->getHook()][$key]
            : $default;
    }

    /**
     * @inheritdoc
     */
    public function getLastDate()
    {
        return ($timestamp = $this->getInfo('last'))
            ? $this->getDatetime()->setTimestamp($timestamp)
            : null;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getNextDate()
    {
        return ($timestamp = wp_next_scheduled($this->getHook()))
            ? $this->getDatetime()->setTimestamp($timestamp)
            : null;
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp()
    {
        return $this->getDate()->getTimestamp();
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * @inheritdoc
     */
    public function log()
    {
        return $this->get('logger');
    }

    /**
     * @inheritdoc
     */
    public function onTest()
    {
        return (bool)$this->get('test', false);
    }

    /**
     * @inheritdoc
     */
    public function updateInfo($key, $value)
    {
        $jobs = get_option('cron_job_infos', []);
        $jobs[$this->getHook()][$key] = $value;
        update_option('cron_job_infos', $jobs, false);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $date = $this->get('date');
        if (!$date instanceof DateTime) :
            $this->set(
                'date',
                $this->getDatetime($date)
            );
        endif;

        $logger = $this->get('logger');
        if (!$logger instanceof Logger) :
            $defaults = [
                'name'    => 'cron'
            ];
            $logger = is_array($logger)
                ? array_merge($defaults, $logger)
                : $defaults;

            $this->set(
                'logger',
                app('logger', [$logger['name'], $logger])
            );
        endif;

        $freq = $this->get('freq');
        $recurrences = wp_get_schedules();
        if (is_array($freq)) :
            if (!$freq_id = $this->get('freq.id')) :
                $freq_id = 'daily';
            else :
                add_filter(
                    'cron_schedules',
                    function () use ($freq) {
                        $attrs = array_merge(
                            [
                                'interval' => DAY_IN_SECONDS,
                                'display'  => __('Once Daily'),
                            ],
                            $freq
                        );

                        return [
                            $attrs['id'] => [
                                'interval' => $attrs['interval'],
                                'display'  => $attrs['display'],
                            ],
                        ];
                    });
            endif;
        else :
            if (is_string($freq)) :
                $freq_id = isset($recurrences[$freq]) ? $freq : 'daily';
            else :
                $freq_id = 'daily';
            endif;
        endif;
        $this->set('freq', $freq_id);
    }
}