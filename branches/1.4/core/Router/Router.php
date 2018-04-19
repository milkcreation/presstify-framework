<?php

namespace tiFy\Core\Router;

use League\Container\Exception\NotFoundException;
use tiFy\App\Core;
use tiFy\Core\Options\Options;
use tiFy\Core\Router\Factory;
use tiFy\Core\Router\Taboox\Admin\ContentHook as TabooxContentHook;

class Router extends Core
{
    /**
     * Liste des classe de rappel des routes déclarées.
     * @var Factory[]
     */
    private static $Factory = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Traitement des paramètres de configuration
        if ($config = self::tFyAppConfig()) :
            foreach ($config as $id => $attrs) :
                self::register($id, $attrs);
            endforeach;
        endif;

        // Déclaration de route
        do_action('tify_router_register');

        // Déclaration des événements
        $this->appAddAction('tify_options_register_node');

        // Fonction d'aide à la saisie
        $this->appDirname() . '/Helpers.php';
    }

    /**
     * Récupération de l'instance de la classe.
     *
     * @return object|self
     */
    public static function get()
    {
        try {
            return self::tFyAppGetContainer(__CLASS__);
        } catch(NotFoundException $e) {
            wp_die($e->getMessage(), '', $e->getCode());
            exit;
        }
    }

    /**
     * Déclaration de sections de boîte à onglet de l'interface d'administration des options.
     *
     * @return void
     */
    public function tify_options_register_node()
    {
        if (!self::$Factory) :
            return;
        endif;

        Options::registerNode(
            [
                'id'    => 'tifyCoreRouter-optionsNode',
                'title' => __('Routes', 'tify')
            ]
        );

        Options::registerNode(
            [
                'id'     => 'tifyCoreRouter-optionsNode--contentHook',
                'parent' => 'tifyCoreRouter-optionsNode',
                'title'  => __('Pages de contenu spéciales', 'tify'),
                'cb'     => TabooxContentHook::class,
            ]
        );
    }

    /**
     * Déclaration d'une route.
     *
     * @param string $id Ientifiant unique de qualification de la route
     * @param array $attrs {
     *      Liste des attributs de configuration.
     *
     *      @var string $title Intitulé de la route
     *      @var string $desc Texte de descritpion de la route
     *      @var string object_type Type d'objet (post|taxonomy) en relation avec la route
     *      @var string object_name Nom de qualification de l'objet en relation (ex: post, page, category, tag ...)
     *      @var string option_name Clé d'index d'enregistrement en base de données
     *      @var string list_order Ordre d'affichage de la liste de selection de l'interface d'administration
     *      @var string show_option_none Intitulé de la liste de selection de l'interface d'administration lorsqu'aucune relation n'a été établie
     * }
     *
     * @return Factory
     */
    public static function register($id, $attrs = [])
    {
        return self::$Factory[$id] = new Factory($id, $attrs);
    }

    /**
     * Récupération de la listes des classes de rappel des routes déclarées.
     *
     * @return array
     */
    final public function getRouteList()
    {
        return self::$Factory;
    }

    /**
     * Récupération d'une classe de rappel d'une route déclarée.
     *
     * @param string $name Identifiant de qualification de la route.
     *
     * @return null|Factory
     */
    final public function getRoute($name)
    {
        if (isset(self::$Factory[$name])) :
            return self::$Factory[$name];
        endif;
    }

    /**
     * Vérification d'existance d'une page associée à l'identifiant de qualification de la route.
     *
     * @param string $name Identifiant de qualification de la route.
     * @param null|int|\WP_Post| $post Post Wordpress courant|Identifiant de qualification du post|Object Post Wordpress.
     *
     * @return bool
     */
    final public function isContentHook($name, $post = null)
    {
        if (!$post = get_post($post)) :
            return false;
        endif;

        if (!$router = $this->getRoute($name)) :
            return false;
        endif;

        return ($router->getSelected() === $post->ID);
    }

    /**
     * Récupération de la page associée à l'identifiant de qualification de la route.
     *
     * @param string $name Identifiant de qualification de la route.
     * @param int $default Valeur de retour par défaut.
     *
     * @return int
     */
    final public function getContentHook($hook_id, $default = 0)
    {
        if (!$router = $this->getRoute($hook_id)) :
            return $default;
        endif;

        return $router->getSelected();
    }
}