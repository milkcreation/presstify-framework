<?php

/**
 * @name Session
 * @desc Gestion d'enregistrement de données de session
 * @package presstiFy
 * @namespace tiFy\User\Session
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\User\Session;

use tiFy\Apps\AppController;
use tiFy\Db\Db;
use tiFy\Db\DbController;
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
     * @var DbController
     */
    private $db;

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('init', null, 0);
        //$this->appAddAction('wp_footer');
    }

    /**
     * Affichage des information de Deboguage.
     *
     * @return string
     */
    public function wp_footer()
    {
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

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        do_action('tify_user_session_register', $this);

        // Initialisation de la base de données
        if (! empty($this->sessionNames)) :
            $this->initDb();

            $this->appAddAction('tify_cron_register');
        endif;
    }

    /**
     * Déclaration de tâche planifiée.
     *
     * @return void
     */
    public function tify_cron_register()
    {
        $this->appServiceGet(Cron::class)->register(
            '_tiFySessionCleanup',
            [
                'title'         => __('Nettoyage de sessions', 'tiFy'),
                'desc'          => __('Suppression de la liste des sessions arrivée à expiration.', 'tiFy'),
                'recurrence'    => 'twicedaily',
                'handle'        => [$this, 'cleanup'],
            ]
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
            $this->db->handle()->query($this->db->handle()->prepare("DELETE FROM " . $this->db->getName() . " WHERE session_expiry < %d", time()));
        endif;
    }

    /**
     * Initialisation de la table de base de données
     * @see https://github.com/kloon/woocommerce-large-sessions
     *
     * @return DbController
     */
    private function initDb()
    {
        $this->db = $this->appServiceGet(Db::class)->register(
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

        $this->db->install();

        return $this->db;
    }

    /**
     * Récupération de la base de données
     *
     * @return DbController
     *
     * @throws \Exception
     */
    public function getDb()
    {
        if (! $this->db instanceof DbController) :
            throw new \Exception(__('La table de base de données de stockage des sessions est indisponible.', 'tify'), 500);
        endif;

        return $this->db;
    }
}