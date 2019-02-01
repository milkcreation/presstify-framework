<?php

/**
 * @see https://github.com/kloon/woocommerce-large-sessions
 */

namespace tiFy\User\Session;

use tiFy\Contracts\Db\DbFactory;
use tiFy\Contracts\User\SessionManager as SessionManagerContract;
use tiFy\Contracts\User\SessionStore;
use tiFy\Cron\Cron;

final class SessionManager implements SessionManagerContract
{
    /**
     * Liste des élements déclarés
     * @var SessionStore[]
     */
    protected $items = [];

    /**
     * Classe de rappel de la base de données
     * @var DbFactory
     */
    private $db;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'init',
            function() {
                events()->trigger('user.session.register', [&$this]);

                // Initialisation de la base de données
                if (!empty($this->items)) :
                    /** @var Cron $cron */
                    $cron = app('cron');
                    $cron->add(
                        'session.cleanup',
                        [
                            'title'         => __('Nettoyage de sessions', 'tiFy'),
                            'desc'          => __('Suppression de la liste des sessions arrivée à expiration.', 'tiFy'),
                            'freq'          => 'twicedaily',
                            'command'       => function () {
                                if (! defined('WP_SETUP_CONFIG') && ! defined('WP_INSTALLING')) :
                                    $this->getDb()->handle()->query(
                                        $this->getDb()->handle()->prepare(
                                            "DELETE FROM " . $this->getDb()->getTableName() . " WHERE session_expiry < %d", time()
                                        )
                                    );
                                endif;
                            },
                        ]
                    );
                endif;
            },
            0
        );

        add_action(
            'wp_footer',
            function() {
                if (config('user.session.debug', false)) :
                    foreach ($this->items as $item) :
                        ?><div style="position:fixed;right:0;bottom:0;width:300px;">
                        <ul>
                            <li>name : <?php echo $item->getName(); ?></li>
                            <li>key : <?php echo $item->getSession('session_key'); ?></li>
                            <li>datatest : <?php echo $item->get('rand_test'); ?></li>
                        </ul>
                        </div><?php
                    endforeach;
                endif;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return $this->items[$name] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDb()
    {
        if (is_null($this->db)) :
            if (!$this->db = db('session')) :
                $this->db = db()->register(
                    'session',
                    [
                        'install'    => true,
                        'name'       => 'tify_session',
                        'primary'    => 'session_key',
                        'col_prefix' => 'session_',
                        'meta'       => false,
                        'columns'    => [
                            'id'     => [
                                'type'           => 'BIGINT',
                                'size'           => 20,
                                'unsigned'       => true,
                                'auto_increment' => true
                            ],
                            'name'     => [
                                'type'           => 'VARCHAR',
                                'size'           => 255,
                                'unsigned'       => false,
                                'auto_increment' => false
                            ],
                            'key'    => [
                                'type' => 'CHAR',
                                'size' => 32,
                                'unsigned'       => false,
                                'auto_increment' => false
                            ],
                            'value'  => [
                                'type' => 'LONGTEXT'
                            ],
                            'expiry' => [
                                'type'     => 'BIGINT',
                                'size'     => 20,
                                'unsigned' => true
                            ]
                        ],
                        'keys'       => ['session_id' => ['cols' => 'session_id', 'type' => 'UNIQUE']],
                    ]
                );
            endif;
        endif;

        if (!$this->db instanceof DbFactory) :
            throw new \Exception(__('La table de base de données de stockage des sessions est indisponible.', 'tify'), 500);
        endif;

        return $this->db;
    }

    /**
     * {@inheritdoc}
     */
    public function register($name, $attrs = [])
    {
        return $this->items[$name] ?? app()->singleton(
            "user.session.store.{$name}",
            function ($name, $attrs = []) {
                return app('user.session.store', [$name, $attrs]);
            }
        )->build([$name, $attrs]);
    }
}