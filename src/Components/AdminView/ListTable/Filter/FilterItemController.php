<?php

namespace tiFy\Components\AdminView\ListTable\Filter;

use Illuminate\Support\Arr;
use tiFy\AdminView\AdminViewInterface;
use tiFy\Partial\Partial;

class FilterItemController implements FilterItemInterface
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Classe de rappel de la vue associée.
     * @var AdminViewInterface
     */
    protected $view;

    /**
     * Liste des attributs de configuration.
     * @return array
     */
    protected $attributes = [
        'content'           => '',
        'attrs'             => [],
        'query_args'        => [],
        'remove_query_args' => [], //['action', 'action2', 'filter_action', '_wp_nonce', '_wp_referer']
        'count_items'       => 0,
        'current'           => false,
        'hide_empty'        => false,
        'show_count'        => false
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration personnalisés.
     * @param AdminViewInterface $view Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], AdminViewInterface $view)
    {
        $this->name = $name;
        $this->view = $view;

        $this->parse($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        if (method_exists($this->view, "get_view_attrs_{$this->name}")) :
            $attrs = call_user_func([$this->view, "get_view_attrs_{$this->name}"], $attrs);
        endif;

        $this->attributes = array_merge(
            $this->attributes,
            $attrs
        );

        if (!$this->get('attrs.href')) :
            $this->set('attrs.href', $this->view->get('page_url'));
        endif;

        if($query_args = $this->get('query_args', [])) :
            $this->set('attrs.href', \add_query_arg($query_args, $this->get('attrs.href')));
        endif;

        if($remove_query_args = $this->get('remove_query_args', [])) :
            $this->set('attrs.href', \remove_query_arg($remove_query_args, $this->get('attrs.href')));
        endif;

        if ($this->get('current')) :
            $this->set('attrs.class', ($class = $this->get('attrs.class') ? "{$class} current" : 'current'));
        endif;

        if (!$this->get('content')) :
            $this->set('content', $this->name);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        Arr::set($this->attributes, $key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        if ($this->get('hide_empty') && !$this->get('count_items', 0)) :
            return '';
        endif;

        return Partial::Tag(
            [
                'tag'   => 'a',
                'attrs' => $this->get('attrs', []),
                'content' => $this->get('content') .
                    ($this->get('show_count') ? " <span class=\"count\">(". $this->get('count_items') .")</span>" : '')
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->display();
    }
}