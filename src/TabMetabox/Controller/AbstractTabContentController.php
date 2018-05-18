<?php

namespace tiFy\TabMetabox\Controller;

use tiFy\Apps\AppController;

class AbstractTabContentController extends AppController implements TabContentControllerInterface
{
    /**
     * Liste des attributs de configuration
     * @var array
     */
    protected $attributes = [];

    /**
     * Nom de qualification de l'environnement d'affichage de la page d'administration.
     * @var string Nom de la page d'option|Nom du type de post|Nom de la taxonomie|Nom du rôle
     */
    protected $object_name = '';

    /**
     * Environnement d'affichage de la page d'administration.
     * @var string options|post_type|taxonomy|user
     */
    protected $object_type = '';

    /**
     * Classe de rappel du controleur de la page d'administration courante de Wordpress.
     * @var \WP_Screen
     */
    protected $wpScreen;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    private function __construct($object_name, $object_type)
    {
        $this->object_name = $object_name;
        $this->object_type = $object_type;

        $this->boot();
    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    protected function __clone()
    {

    }

    /**
     * Court-circuitage de l'implémentation.
     *
     * @return void
     */
    protected function __wakeup()
    {

    }

    /**
     * Création d'un instance du controleur.
     *
     * @return static
     */
    final public static function create($object_name, $object_type)
    {
        return new static($object_name, $object_type);
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Pré-Chargement de la page d'administration courante de Wordpress.
     *
     * @param \WP_Screen $wp_screen Classe de rappel du controleur de la page d'administration courante de Wordpress.
     *
     * @return void
     */
    final public function _load($wp_screen)
    {
        $this->wpScreen = $wp_screen;

        $this->load($wp_screen);
    }

    /**
     * {@inheritdoc}
     */
    public function load($wp_screen)
    {
        $this->wpScreen = $wp_screen;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectName()
    {
        return $this->object_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectType()
    {
        return $this->object_type;
    }

    /**
     * Récupération de l'affichage depuis l'instance.
     *
     * @return string
     */
    public function __invoke()
    {
        return call_user_func_array([$this, 'display'], func_get_args());
    }
}