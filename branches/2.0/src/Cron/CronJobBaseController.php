<?php

namespace tiFy\Cron;

use \DateTime;
use \DateTimeZone;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use tiFy\Contracts\Cron\CronJobInterface;
use tiFy\Kernel\Parameters\AbstractParametersBagIterator;

class CronJobBaseController extends AbstractParametersBagIterator implements CronJobInterface
{
    /**
     * Liste des attributs de configuration.
     * @var array {
     *      @var string $title Intitulé de qualification.
     *      @var string $description Description.
     *      @var int|string|DateTime $date Date de déclenchement de la première itération.
     *      @var string $freq Fréquence d'exécution des itérations.
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
     * Classe de rappel de journalisation
     * @var \Monolog\Logger
     */
    private $logger = null;

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
            set_time_limit(0);

            is_callable($this->getCommand())
                ? call_user_func_array($this->getCommand(), [$this->getArgs(), $this])
                : $this->exec();
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function exec()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getArgs()
    {
        return $this->get('args', []);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand()
    {
        return $this->get('command');
    }

    /**
     * {@inheritdoc}
     */
    public function getDate()
    {
        return $this->get('date');
    }

    /**
     * {@inheritdoc}
     */
    public function getDatetime($time = 'now')
    {
        return new DateTime($time, new DateTimeZone(get_option('timezone_string')));
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * {@inheritdoc}
     */
    public function getFrequency()
    {
        return $this->get('freq');
    }

    /**
     * {@inheritdoc}
     */
    public function getHook()
    {
        return $this->get('hook');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getNext()
    {
        return ($timestamp = wp_next_scheduled($this->getHook()))
            ? $this->getDatetime()->setTimestamp($timestamp)
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp()
    {
        return $this->getDate()->getTimestamp();
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * {@inheritdoc}
     */
    public function onTest()
    {
        return (bool)$this->get('test', false);
    }

    /**
     * {@inheritdoc}
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

        if ($log = $this->get('log')) :
            $defaults = [
                'format'  => "%datetime% %level_name% \"%message%\" %context% %extra%\n",
                'rotate'  => 10,
                'name'    => $this->getName(),
                'basedir' => WP_CONTENT_DIR . '/uploads/log',
            ];
            $this->set(
                'log',
                is_array($log) ? array_merge($defaults, $log) : $defaults
            );
        endif;

        $freq = $this->get('freq');
        $recurrences = wp_get_schedules();
        if (is_array($freq)) :
            if (!$freq = $this->get('freq.id')) :
                $freq = 'daily';
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
                $freq = isset($recurrences[$freq]) ? $freq : 'daily';
            else :
                $freq = 'daily';
            endif;
        endif;
        $this->set('freq', $freq);
    }

    /**
     * Initialisation de la journalisation
     */
    final public function initLogger()
    {
        if (!$attrs = $this->getLog()) :
            return;
        endif;

        $output = $attrs['format'];
        $formatter = new LineFormatter($output);
        $stream = new RotatingFileHandler($attrs['basedir'] . '/' . $attrs['name'] . '.log', $attrs['rotate']);
        $stream->setFormatter($formatter);
        $this->Logger = new Logger($this->getId());
        if ($timezone = get_option('timezone_string')) :
            $this->Logger->setTimezone(new \DateTimeZone($timezone));
        endif;
        $this->Logger->pushHandler($stream);
    }

    /**
     * Récupération de la classe de rappel de journalisation
     *
     * @return \Monolog\Logger
     */
    final public function getLogger()
    {
        return $this->Logger;
    }

    /**
     *
     */
    final public function loggerAddExtras($extras)
    {
        if ($this->Logger->getProcessors()) :
            $this->Logger->popProcessor();
        endif;

        $this->Logger->pushProcessor(function ($record) use ($extras) {
            $record['extra'] = $extras;

            return $record;
        });
    }
}