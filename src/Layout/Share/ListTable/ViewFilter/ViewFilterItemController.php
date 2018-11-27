<?php

namespace tiFy\Layout\Share\ListTable\ViewFilter;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\Layout\Share\ListTable\Contracts\ListTableInterface;
use tiFy\Layout\Share\ListTable\Contracts\ViewFilterItemInterface;

class ViewFilterItemController extends ParamsBag implements ViewFilterItemInterface
{
    /**
     * Liste des attributs de configuration.
     * @var array {
     *      @var string $content Contenu du lien de vue filtrée (chaîne de caractère ou éléments HTML).
     *      @var array $attrs Liste des attributs de balise HTML.
     *      @var array $query_args Tableau associatif des arguments passés en requête dans l'url du lien de vue filtrée
     *      @var array $remove_query_args Tableau indexé des arguments supprimés de l'url de requête du lien de vue filtrée
     *      @var int $count_items Nombre d'élément correspondant à la vue filtrée
     *      @var bool $current Définie si la vue courante correspond à la vue filtrée
     *      @var bool $hide_empty Masque le lien si aucun élément ne correspond à la vue filtrée
     *      @var bool|string $show_count Affiche le nombre d'éléments correspondant dans le lien de la vue filtrée false|true|'(%d)' où %d correspond au nombre d'éléments
     * }
     */
    protected $attributes = [
        'content'           => '',
        'attrs'             => [],
        'query_args'        => [],
        'remove_query_args' => [], //['action', 'action2', 'filter_action', '_wp_nonce', '_wp_referer']
        'count_items'       => 0,
        'current'           => false,
        'hide_empty'        => false,
        'show_count'        => false,
    ];

    /**
     * Instance de la disposition associée.
     * @var ListTableInterface
     */
    protected $layout;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration personnalisés.
     * @param ListTableInterface $layout Instance de la disposition associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], ListTableInterface $layout)
    {
        $this->name = $name;
        $this->layout = $layout;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->display();
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        if ($this->get('hide_empty') && !$this->get('count_items', 0)) :
            return '';
        endif;

        return partial(
            'tag',
            [
                'tag'     => 'a',
                'attrs'   => $this->get('attrs', []),
                'content' => $this->get('content'),
                'after'   => $this->get('show_count')
                    ? " <span class=\"count\">(" . $this->get('count_items') . ")</span>"
                    : ''
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->get('attrs.href')) :
            $this->set('attrs.href', $this->layout->get('page_url'));
        endif;

        if ($query_args = $this->get('query_args', [])) :
            $this->set('attrs.href', \add_query_arg($query_args, $this->get('attrs.href')));
        endif;

        if ($remove_query_args = $this->get('remove_query_args', [])) :
            $this->set('attrs.href', \remove_query_arg($remove_query_args, $this->get('attrs.href')));
        endif;

        if ($this->get('current')) :
            $this->set('attrs.class', ($class = $this->get('attrs.class') ? "{$class} current" : 'current'));
        endif;

        if (!$this->get('content')) :
            $this->set('content', $this->name);
        endif;
    }
}