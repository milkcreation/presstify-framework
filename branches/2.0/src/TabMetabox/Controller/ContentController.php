<?php

namespace tiFy\TabMetabox\Controller;

use Illuminate\Support\Arr;
use tiFy\Apps\AppController;

class ContentController extends AppController implements ContentInterface
{
    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes = [];

    /**
     * Nom de qualification de l'environnement d'affichage.
     * @var string Nom de la page d'option|Nom du type de post|Nom de la taxonomie|Nom du rôle
     */
    protected $object_name = '';

    /**
     * Environnement d'affichage.
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
     * @param string $object_name Nom de qualification de l'environnement d'affichage.
     * @param string $object_type Environnement d'affichage.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($object_name, $object_type, $attrs = [])
    {
        $this->object_name = $object_name;
        $this->object_type = $object_type;

        $this->parse($attrs);

        $this->appTemplates(
            array_merge(
                [
                    'directory' => $this->appDirname() . '/templates'
                ],
                $this->get('templates', [])
            )
        );

        if (method_exists($this, 'boot')) :
            $this->boot();
        endif;
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
    public function all()
    {
        return $this->attributes;
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
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = '')
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key, $default = null)
    {
        return Arr::has($this->attributes, $key);
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
     * {@inheritdoc}
     */
    public function load($wp_screen)
    {
        $this->wpScreen = $wp_screen;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->attributes = array_merge(
            $this->attributes,
            array_merge(
                $this->defaults(),
                $attrs
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);

        return $this;
    }
}