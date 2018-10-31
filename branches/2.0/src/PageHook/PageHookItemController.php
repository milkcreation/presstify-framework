<?php

namespace tiFy\PageHook;

use tiFy\Contracts\App\AppInterface;
use tiFy\Kernel\Params\ParamsBag;

class PageHookItemController extends ParamsBag implements PageHookItemInterface
{
    /**
     * Classe de rappel du controleur de l'application associée.
     * @var AppInterface
     */
    protected $app;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Liste des attributs de configuration.
     * @var array {
     *      Liste des attributs de configuration.
     *
     * @var string $name Nom de qualification d'enregistrement en base de donnée.
     * @var string $title Intitulé de qualification.
     * @var string $desc Texte de description.
     * @var string $object_type Type d'objet Wordpress. post|taxonomy @todo
     * @var string $object_name Nom de qualification de l'objet Wordpress. (ex. post|page|category|tag ...)
     * @var int $id Identifiant de qualification de la page d'accroche associée.
     * @var string list_order Ordre d'affichage de la liste de selection de l'interface d'administration
     * @var string show_option_none Intitulé de la liste de selection de l'interface d'administration lorsqu'aucune relation n'a été établie
     * }
     */
    protected $attributes = [
        'option_name'      => '',
        'title'            => '',
        'desc'             => '',
        'object_type'      => 'post',
        'object_name'      => 'page',
        'id'               => 0,
        'listorder'        => 'menu_order, title',
        'show_option_none' => '',
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     * @param AppInterface $app Classe de rappel du controleur de l'application associée.
     *
     * @return void
     */
    public function __construct($name,  $attrs = [], $app)
    {
        $this->name = $name;
        $this->app = $app;

        parent::__construct($attrs, $app);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'option_name'      => 'tFyPageHook_' . $this->name,
            'title'            => $this->name,
            'desc'             => '',
            'object_type'      => 'post',
            'object_name'      => 'page',
            'id'               => 0,
            'listorder'        => 'menu_order, title',
            'show_option_none' => __('Aucune page choisie', 'tify'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return (int)$this->get('id', 0);
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
    public function getObjectType()
    {
        return $this->get('object_type');
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectName()
    {
        return $this->get('object_name');
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionName()
    {
        return $this->get('option_name');
    }

    /**
     * {@inheritdoc}
     */
    public function getPermalink()
    {
        return ($id = $this->getId())
            ? \get_permalink($id)
            : '';
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
    public function isCurrent($post = null)
    {
        if (!$post = \get_post($post)) :
            return false;
        endif;

        return $this->getId() === $post->ID;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->getId()) :
            $this->set('id', (int)get_option($attrs['option_name'], 0));
        endif;

        return $attrs;
    }
}