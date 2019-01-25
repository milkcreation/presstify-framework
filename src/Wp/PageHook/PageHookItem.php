<?php

namespace tiFy\Wp\PageHook;

use tiFy\Contracts\Wp\PageHookItem as PageHookItemContract;
use tiFy\Kernel\Params\ParamsBag;
use tiFy\Wp\Query\Post;

class PageHookItem extends ParamsBag implements PageHookItemContract
{
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
     * Instance du post associé.
     * @var Post
     */
    protected $post;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return void
     */
    public function __construct($name,  $attrs = [])
    {
        $this->name = $name;

        parent::__construct($attrs);
    }

    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return [
            'option_name'      => 'page_hook_' . $this->name,
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
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getObjectType()
    {
        return $this->get('object_type');
    }

    /**
     * @inheritdoc
     */
    public function getObjectName()
    {
        return $this->get('object_name');
    }

    /**
     * @inheritdoc
     */
    public function getOptionName()
    {
        return $this->get('option_name');
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * @inheritdoc
     */
    public function exists()
    {
        return $this->post() instanceof Post;
    }

    /**
     * @inheritdoc
     */
    public function is($post = null)
    {
        if (!$post = get_post($post)) :
            return false;
        endif;

        return $this->post()->getId() === $post->ID;
    }

    /**
     * @inheritdoc
     */
    public function post()
    {
        if (!$this->post instanceof Post) :
            if (!$post_id = $this->get('id')) :
                $post_id = (int)get_option($this->get('option_name'), 0);
                $this->set('id', $post_id);
            endif;
            if ($post = get_post($this->get('id', 0))) :
                $this->post = new Post($post);
            endif;
        endif;

        return $this->post;
    }
}