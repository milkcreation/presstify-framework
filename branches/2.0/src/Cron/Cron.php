<?php

namespace tiFy\Cron;

use Illuminate\Support\Collection;
use tiFy\Contracts\Cron\CronJobInterface;
use tiFy\Cron\ScheduleBaseController;

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
 * Tester une tâche planifiée de puis le navigateur ou en ligne de commande.
 * > http(s)://%site_url%/?job=%task%
 */

final class Cron
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
                foreach(get_option('cron_job', []) as $hook) :
                    if (!$collect->firstWhere('hook', $hook)) :
                        wp_clear_scheduled_hook($hook);
                    endif;
                endforeach;

                update_option(
                    'cron_job',
                    $collect->pluck('hook')->all()
                );

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
     * @return CronJobInterface
     */
    private function _register($name, $attrs = [])
    {
        /** @var CronJobInterface $item */
        if ($item = $this->get($name)) :
            return $item;
        else :
            $item = ($controller = $attrs['controller'])
                ? new $controller($name, $attrs)
                : app('cron.job', [$name, $attrs]);
        endif;

        if (!$item instanceof CronJobInterface) :
            return;
        endif;

        if (($freq = wp_get_schedule($item->getHook())) && ($freq !== $item->getFrequency())) :
            wp_clear_scheduled_hook($item->getHook());
        endif;

        if (!wp_next_scheduled ($item->getHook())) :
            wp_schedule_event($item->getTimestamp(), $item->getFrequency(), $item->getHook());
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
        config()->set("cron.{$name}", $attrs);

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
}