<?php

/**
 * @name Session
 * @desc Gestion d'enregistrement de données de session.
 * @package tiFy
 * @namespace tiFy\User\Session
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 *
 * @see https://github.com/kloon/woocommerce-large-sessions
 */

namespace tiFy\User\Session;

use tiFy\App\AppController;
use tiFy\Contracts\Db\DbItemInterface;
use tiFy\Db\Db;
use tiFy\Cron\Cron;

final class Session extends AppController
{
    /**
     * Liste des noms de qualification de sessions déclarées
     * @var string[]
     */
    private $sessionNames = [];

    /**
     * Classe de rappel de la base de données
     * @var DbControllerInterface
     */
    private $db;

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        add_action(
            'init',
            function() {
                do_action('tify_user_session_register', $this);

                // Initialisation de la base de données
                if (! empty($this->sessionNames)) :
                    /** @var Db $db */
                    $db = app('db');
                    $db->add(
                        '_tiFySession',
                        [
                            'install'    => false,
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

                    /** @var Cron $cron */
                    $cron = app('cron');
                    $cron->add(
                        'session.cleanup',
                        [
                            'title'         => __('Nettoyage de sessions', 'tiFy'),
                            'desc'          => __('Suppression de la liste des sessions arrivée à expiration.', 'tiFy'),
                            'freq'          => 'twicedaily',
                            'command'       => [$this, 'cleanup'],
                        ]
                    );
                endif;
            },
            0
        );

        add_action(
            'wp_footer',
            function() {
                return;
                if (! empty($this->sessionNames)) :
                    foreach ($this->sessionNames as $name) :
                        if (!$session = $this->get($name)) :
                            continue;
                        endif;

                        ?><div style="position:fixed;right:0;bottom:0;width:300px;">
                        <ul>
                            <li>name : <?php echo $name; ?></li>
                            <li>key : <?php echo $session->getSession('session_key'); ?></li>
                            <li>datatest : <?php echo $session->get('rand_test'); ?></li>
                        </ul>
                        </div><?php
                    endforeach;
                endif;
            }
        );
    }

    /**
     * Déclaration d'une session.
     *
     * @param string $name Nom de qualification de la session.
     * @param array $attrs Attributs de configuration.
     *
     * @return null|StoreInterface
     */
    public function register($name, $attrs = [])
    {
        $alias = "tfy.user.session.{$name}";
        if ($this->appServiceHas($alias)) :
            return null;
        endif;

        $this->appServiceShare($alias, new Store($name, $attrs));
        array_push($this->sessionNames, $name);

        return $this->appServiceGet($alias);
    }

    /**
     * Récupération d'une session
     *
     * @param string $name Nom de qualification de la session
     *
     * @return null|object|StoreInterface
     */
    public function get($name)
    {
        $alias = "tify.user.session.{$name}";
        if ($this->appServiceHas($alias)) :
            return $this->appServiceGet($alias);
        endif;
    }

    /**
     * Nettoyage des sessions arrivée à expiration.
     *
     * @return void
     */
    public function cleanup()
    {
        if (! defined('WP_SETUP_CONFIG') && ! defined('WP_INSTALLING')) :
            $this->getDb()->handle()->query(
                $this->getDb()->handle()->prepare(
                        "DELETE FROM " . $this->getDb()->getTableName() . " WHERE session_expiry < %d", time()
                )
            );
        endif;
    }

    /**
     * Récupération de la base de données
     *
     * @return DbItemInterface
     *
     * @throws \Exception
     */
    public function getDb()
    {
        if (!$this->db) :
            /** @var Db $db */
            $db = app('db');
            $this->db = $db->get('_tiFySession');
        endif;

        if (!$this->db instanceof DbItemInterface) :
            throw new \Exception(__('La table de base de données de stockage des sessions est indisponible.', 'tify'), 500);
        endif;

        return $this->db;
    }
}