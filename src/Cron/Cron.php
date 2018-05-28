<?php

/**
 * @name Cron
 * @package PresstiFy
 * @subpackage Core
 * @namespace tiFy\Cron
 * @desc Gestion de tâches planifiées
 * @author Jordy Manner
 * @copyright Tigre Blanc Digital
 * @version 1.2.369
 * @see https://developer.wordpress.org/plugins/cron/hooking-into-the-system-task-scheduler/
 */

namespace tiFy\Cron;

use tiFy\Apps\AppController;
use tiFy\Cron\Admin\ViewList;
use tiFy\Cron\Schedule;
use tiFy\Templates\Templates;

/**
 * USAGE
 *
 * Configurer une tâche planifiée
 * 1. Dans le fichier wp-config.php, désactiver l'appel navigateur des tâches cron
 * > define('DISABLE_WP_CRON', true);
 *
 * 2. Sur le serveur (linux) configurer l'appel cli des tâches planifiées
 * > $ crontab -e
 * > $ * * * * * curl -I http(s)://%site_url%/wp-cron.php?doing_wp_cron > /dev/null 2>&1
 *
 * Tester une tâche planifiée de puis le navigateur ou en ligne de commande
 * > http(s)://%site_url%/?tFyCronDoing=%task_id%
 */

final class Cron extends AppController
{
    /**
     * Listes des attributs de configuration des tâches planifiées
     * @var \tiFy\Cron\Schedule[]
     */
    protected $schedules = [];

    /**
     * Initialisation du controleur
     *
     * @return void
     */
    public function appBoot()
    {
        return;
        $this->appAddAction('init');
        $this->appAddAction('tify_templates_register');
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        // Déclaration des tâches planifiées configurées.
        foreach ($this->appConfig() as $schedule_id => $schedules_attrs) :
            $this->register($schedule_id, $schedules_attrs);
        endforeach;

        // Déclaration des tâches planifiées annexes
        do_action('tify_cron_register');

        // Exécution d'une tâche à la volée (test)
        if (!$doing = $this->appRequest('get')->get('tFyCronDoing', '')) :
            return;
        endif;

        if ($schedule = $this->get($doing)) :
            do_action_ref_array($schedule['hook'], [$schedule]);
            exit;
        endif;
    }

    /**
     * Déclaration de templates.
     *
     * @return void
     */
    public function tify_templates_register()
    {
        Templates::register(
            'tFyCoreCronList',
            [
                'cb'         => ViewList::class,
                'admin_menu' => [
                    'menu_slug'   => 'tFyCoreCronList',
                    'parent_slug' => 'tools.php',
                    'page_title'  => __('Gestion des tâches planifiées', 'tify'),
                    'menu_title'  => __('Tâches planifiées', 'tify'),
                ],
            ],
            'admin'
        );
    }

    /**
     * Déclaration d'un tâche planifiée.
     *
     * @param string $name Identifiant de qualification.
     * @param array $attrs {
     *      Liste des attribut de configuration.
     *
     *      @var string $title Intitulé de la tâche planifiée.
     *      @var string $desc Description de la tâche planifiée.
     *      @var int $timestamp Date de lancement de la tâche planifiée.
     *      @var string $recurrence Fréquence de répétition de la tâche planifiée.
     *      @var string|callable|object $handle Execution du traitement de la tâche planifiée.
     *      @var array $args Variables passées en argument lors du traitement de la tâche planifiée.
     *      @var bool|array $log Attributs de journalisation des données.
     *      @var bool $unregister Activation du désenregistrement d'une tâche planifiée.
     * }
     * @return array
     */
    public function register($name, $attrs = [])
    {
        return;
        if (isset($this->schedules[$name])) :
            return;
        endif;

        $defaults = [
            'title'      => $name,
            'desc'       => '',
            'timestamp'  => date('U', mktime(2, 0, 0, 5, 27, 2003)),
            'recurrence' => 'daily',
            'handle'     => '',
            'log'        => true,
            'args'       => [],
            'unregister' => false,
        ];

        // Traitement des attributs de configuration
        $attrs = array_merge($defaults, $attrs);

        // Activaction/Désactivation du désenregistrement
        $unregister = $attrs['unregister'] ? true : false;
        unset($attrs['unregister']);

        // Identifiant unique
        $attrs['id'] = $name;

        // Identifiant unique d'accorche de la tâche planifiée
        $attrs['hook'] = 'tFyCron_' . $name;

        // Date GMT d'exécution de la tâche
        $attrs['timestamp'] = get_gmt_from_date(date('Y-m-d H:i:s', $attrs['timestamp']), 'U');

        // Traitement de la récurrence
        $recurrences = \wp_get_schedules();
        if (is_string($attrs['recurrence']) && !isset($recurrences[$attrs['recurrence']])) :
            $attrs['recurrence'] = 'daily';
        elseif (is_array($attrs['recurrence'])) :
            if (!isset($attrs['recurrence']['id'])) :
                $attrs['recurrence'] = 'daily';
            else :
                $r = \wp_parse_args(
                    $attrs['recurrence'],
                    [
                        'interval' => DAY_IN_SECONDS,
                        'display'  => __('Once Daily'),
                    ]
                );
                add_filter(
                    'cron_schedules',
                    function () use ($r) {
                        return [
                            $r['id'] => [
                                'interval' => $r['interval'],
                                'display'  => $r['display'],
                            ],
                        ];
                    });

                $attrs['recurrence'] = $r['id'];
            endif;
        endif;

        // Traitement de la classe de surcharge
        if (!$attrs['handle']) :
            $classname = Schedule::class;
            $attrs['handle'] = $classname . '::_handle';
        endif;

        // Traitement de la journalisation
        if ($attrs['log']) :
            $logdef = [
                'format'  => "%datetime% %level_name% \"%message%\" %context% %extra%\n",
                'rotate'  => 10,
                'name'    => $name,
                'basedir' => WP_CONTENT_DIR . '/uploads/log',
            ];
            $attrs['log'] = !is_array($attrs['log']) ? $logdef : \wp_parse_args($attrs['log'], $logdef);
        endif;

        // Définition des attributs de configuration
        $this->schedules[$name] = $attrs;

        // Ajustement de la récurrence
        if (($schedule = \wp_get_schedule($attrs['hook'], [$attrs])) && ($schedule !== $attrs['recurrence'])) :
            $this->unregister($name);
        elseif ($unregister) :
            $this->unregister($name);
        endif;

        if (!\wp_get_schedule($attrs['hook'], [$attrs])) :
            \wp_schedule_event($attrs['timestamp'], $attrs['recurrence'], $attrs['hook'], [$attrs]);
            $this->schedules[$name] = $attrs;
        endif;

        \add_action($attrs['hook'], $attrs['handle']);

        return $this->schedules[$name];
    }

    /**
     * Désenregistrement d'un tâche planifiée
     *
     * @param string $name Identifiant de qualification d'une tâche planifiée déclarée.
     *
     * @return void
     */
    public function unregister($name)
    {
        if (! $crons = _get_cron_array()) :
            return;
        endif;
        if (! $schedule = $this->get($name)) :
            return;
        endif;

        foreach ($crons as $timestamp => $cron) :
            if (!isset($cron[$schedule['hook']])) :
                continue;
            endif;
            foreach ($cron[$schedule['hook']] as $key => $attrs) :
                \wp_unschedule_event($timestamp, $schedule['hook'], $attrs['args']);
            endforeach;
        endforeach;

        unset($this->schedules[$name]);
    }

    /**
     * Récupération de la liste des tâches planifiées déclarées
     *
     * @return array
     */
    public function getList()
    {
        return $this->schedules;
    }

    /**
     * Récupération d'une tâche planifiée déclarée
     *
     * @param string $name Identifiant unique de qualification de la tâche planifiée
     *
     * @return array
     */
    public function get($name)
    {
        if (isset($this->schedules[$name])) :
            return $this->schedules[$name];
        endif;
    }
}