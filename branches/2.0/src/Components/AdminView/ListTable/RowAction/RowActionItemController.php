<?php

namespace tiFy\Components\AdminView\ListTable\RowAction;

use Illuminate\Support\Arr;
use tiFy\AdminView\AdminViewInterface;
use tiFy\Components\AdminView\ListTable\Item\ItemInterface;
use tiFy\Partial\Partial;

class RowActionItemController implements RowActionItemInterface
{
    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Données de l'élément courant.
     * @var ItemInterface
     */
    protected $item;

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
        'content'    => '',
        'attrs'      => [],
        'query_args' => [],
        'nonce'      => true,
        'referer'    => true
    ];

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration personnalisés.
     * @param ItemInterface $item Données de l'élément courant.
     * @param AdminViewInterface $view Classe de rappel de la vue associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], $item, AdminViewInterface $view)
    {
        $this->name = $name;
        $this->item = $item;
        $this->view = $view;

        $this->attributes = array_merge(
            $this->attributes,
            $this->defaults()
        );

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
    public function defaults()
    {
        return [];
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
    public function getNonce()
    {
        if (($item_index_name = $this->view->param('item_index_name')) && isset($this->item->{$item_index_name})) :
            $item_index = $this->item->{$item_index_name};
        else :
            $item_index = '';
        endif;

        if(!$item_index) :
        elseif(!is_array($item_index)) :
            $item_index = array_map('trim', explode(',', $item_index));
        endif;

        if (!$item_index || (count($item_index) === 1)) :
            $nonce_action = $this->view->param('singular') . '-' . $this->name;
        else :
            $nonce_action = $this->view->param('plural') . '-' . $this->name;
        endif;

        if ($item_index && count($item_index) === 1) :
            $nonce_action .= '-' . reset($item_index);
        endif;

        return \sanitize_title($nonce_action);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        if (method_exists($this->view, "get_row_action_attrs_{$this->name}")) :
            $attrs = call_user_func([$this->view, "get_row_action_attrs_{$this->name}"], $attrs);
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

        if ($nonce = $this->get('nonce')) :
            if ($nonce === true) :
                $nonce = $this->view->get('page_url');
            endif;

            $this->set('attrs.href', \wp_nonce_url($this->get('attrs.href'), $nonce));
        endif;

        if ($referer = $this->get('referer')) :
            if ($referer === true) :
                $referer = set_url_scheme('//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            endif;

            $this->set(
                'attrs.href',
                \add_query_arg(
                    [
                        '_wp_http_referer' => urlencode(
                            wp_unslash($referer)
                        )
                    ],
                    $this->get('attrs.href')
                )
            );
        endif;

        // Argument de requête par défaut
        /*$default_query_args = [
            'action' => $row_action_name
        ];
        if (($item_index_name = $this->getParam('item_index_name')) && isset($item->{$item_index_name})) :
            $default_query_args[$item_index_name] = $item->{$item_index_name};
        endif;
        $href = \add_query_arg(
            $default_query_args,
            $href
        );*/

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