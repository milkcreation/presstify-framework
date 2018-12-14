<?php

namespace tiFy\Cron;

use Illuminate\Support\Collection;
use tiFy\Contracts\Cron\Cron as CronContract;
use tiFy\Contracts\Cron\CronJobInterface;

/**
 * USAGE
 *
 * Configurer une tâche planifiée
 * 1. Dans le fichier wp-config.php, désactiver l'appel navigateur des tâches cron (recommandé).
 * > define('DISABLE_WP_CRON', true);
 *
 * 2. Sur le serveur (MacOS ou Linux), configurer l'exécution cli des tâches planifiées.
 * > $ crontab -e
 * > $ * * * * * curl -I http(s)://%site_url%/wp-cron.php?doing_wp_cron > /dev/null 2>&1
 *
 * Tester une tâche planifiée depuis le navigateur ou en console. Le mode test de la tâche doit être actif.
 * IMPORTANT : N'utiliser cette fonctionnalité qu'en développement uniquement.
 * Désactiver absolument le mode test en production.
 * > http(s)://%site_url%/?job=%task%
 */

final class Cron implements CronContract
{
    /**
     * Listes des tâches planifiées déclarées.
     * @var CronJobInterface[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'init',
            function () {
                foreach (config('cron', []) as $name => $attrs) :
                    $this->_register($name, $attrs);
                endforeach;

                $collect = new Collection($this->items);
                foreach(get_option('cron_job_infos', []) as $hook => $attrs) :
                    if (!$collect->firstWhere('hook', $hook)) :
                        $this->clear($hook);
                    endif;
                endforeach;

                $jobs = $collect->mapWithKeys(function ($item) {
                    return [$item['hook'] => []];
                })->all();

                update_option(
                    'cron_job_infos',
                    array_merge($jobs, get_option('cron_job_infos', [])),
                    false
                );

                if ($jobs = $this->all()) :
                    pattern()->register(
                        'cron.layout.list',
                        [
                            'admin_menu' => [
                                'menu_slug'   => 'CronLayoutList',
                                'parent_slug' => 'tools.php',
                                'page_title'  => __('Gestion des tâches planifiées', 'tify'),
                                'menu_title'  => __('Tâches planifiées', 'tify')
                            ],
                            'content' => function () {
                                $jobs = $this->all();

                                return view()
                                    ->setDirectory(__DIR__ . '/views')
                                    ->make('job-list', compact('jobs'));
                            }
                        ]
                    );
                endif;

                if (($job = request()->get('job', '')) && ($item = $this->get($job))) :
                    do_action($item->getHook());
                    exit;
                endif;
            },
            999999
        );
    }

    /**
     * Enregistrement d'une tâche planifiée.
     *
     * @param string $name Identifiant de qualification.
     * @param array $attrs Liste des attribut de configuration.
     *
     * @return null|CronJobInterface
     */
    private function _register($name, $attrs = [])
    {
        /** @var CronJobInterface $item */
        if ($item = $this->get($name)) :
            return $item;
        else :
            $item = (isset($attrs['controller']) && ($controller = $attrs['controller']))
                ? new $controller($name, $attrs)
                : app('cron.job', [$name, $attrs]);
        endif;

        if (!$item instanceof CronJobInterface) :
            return null;
        endif;

        if (($freq = wp_get_schedule($item->getHook())) && ($freq !== $item->getFrequency())) :
            $this->clear($item->getHook());
        endif;

        if (!wp_next_scheduled ($item->getHook())) :
            wp_schedule_event(
                $item->getTimestamp(),
                $item->getFrequency(),
                $item->getHook()
            );
        endif;

        return $this->items[$name] = $item;
    }

    /**
     * Déclaration d'une tâche planifiée.
     *
     * @param string $name Identifiant de qualification.
     * @param array $attrs Liste des attribut de configuration.
     *
     * @return $this
     */
    public function add($name, $attrs)
    {
        config()->set(
            "cron",
            array_merge(
                [$name => $attrs],
                config('cron', [])
            )
        );

        return $this;
    }

    /**
     * Récupération de la liste des tâches planifiées déclarées.
     *
     * @return CronJobInterface[]
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Suppression d'une tâche planifiée selon son identifiant d'action.
     *
     * @param string $hook Identifiant de qualification de l'action.
     *
     * @return $this
     */
    public function clear($hook)
    {
        wp_clear_scheduled_hook($hook);

        if (($jobs = get_option('cron_job_infos', [])) && isset($jobs[$hook])) :
            unset($jobs[$hook]);
            update_option('cron_job_infos', $jobs, false);
        endif;

        return $this;
    }

    /**
     * Récupération d'une tâche planifiée déclarée.
     *
     * @param string $name Nom de qualification de l'élément.
     *
     * @return null|CronJobInterface
     */
    public function get($name)
    {
        return isset($this->items[$name]) ? $this->items[$name] : null;
    }
}