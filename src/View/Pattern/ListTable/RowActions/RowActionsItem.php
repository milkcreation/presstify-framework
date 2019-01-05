<?php

namespace tiFy\View\Pattern\ListTable\RowActions;

use tiFy\Kernel\Params\ParamsBag;
use tiFy\View\Pattern\ListTable\Contracts\ListTable;
use tiFy\View\Pattern\ListTable\Contracts\RowActionsItem as RowActionsItemContract;

class RowActionsItem extends ParamsBag implements RowActionsItemContract
{
    /**
     * Liste des attributs de configuration.
     * @return array {
     *      @var string $content Contenu du lien (chaîne de caractère ou éléments HTML).
     *      @var array $attrs Liste des attributs complémentaires de la balise du lien.
     *      @var array $query_args Tableau associatif des arguments passés en requête dans l'url du lien.
     *      @var bool|string $nonce Activation de la création de l'identifiant de qualification de la clef de
     *                              sécurisation passée en requête dans l'url du lien|Identifiant de qualification
     *                              de la clef de sécurisation.
     *      @var bool|string $referer Activation de l'argument de l'url de référence passée en requête dans l'url du
     *                                lien.
     * }
     */
    protected $attributes = [
        'content'    => '',
        'attrs'      => [],
        'query_args' => [],
        'nonce'      => true,
        'referer'    => true
    ];

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Instance du motif d'affichage associé.
     * @var ListTable
     */
    protected $pattern;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification.
     * @param array $attrs Liste des attributs de configuration personnalisés.
     * @param ListTable $pattern Instance du motif d'affichage associé.
     *
     * @return void
     */
    public function __construct($name, $attrs, ListTable $pattern)
    {
        $this->name = $name;
        $this->pattern = $pattern;

        parent::__construct($attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->render();
    }

    /**
     * {@inheritdoc}
     */
    public function getNonce()
    {
        /*if (($item_index_name = $this->pattern->param('item_index_name')) && isset($this->item->{$item_index_name})) :
            $item_index = $this->item->{$item_index_name};
        else :*/
            $item_index = '';
        //endif;

        if(!$item_index) :
        elseif(!is_array($item_index)) :
            $item_index = array_map('trim', explode(',', $item_index));
        endif;

        if (!$item_index || (count($item_index) === 1)) :
            $nonce_action = $this->pattern->param('singular') . '-' . $this->name;
        else :
            $nonce_action = $this->pattern->param('plural') . '-' . $this->name;
        endif;

        if ($item_index && count($item_index) === 1) :
            $nonce_action .= '-' . reset($item_index);
        endif;

        return sanitize_title($nonce_action);
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->get('attrs.href')) :
            $this->set('attrs.href', $this->pattern->param('page_url'));
        endif;

        if($query_args = $this->get('query_args', [])) :
            $this->set('attrs.href', \add_query_arg($query_args, $this->get('attrs.href')));
        endif;

        if ($nonce = $this->get('nonce')) :
            if ($nonce === true) :
                $nonce = $this->pattern->param('page_url');
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
    public function render()
    {
        if ($this->get('hide_empty') && !$this->get('count_items', 0)) :
            return '';
        endif;

        return partial(
            'tag',
            [
                'tag'       => 'a',
                'attrs'     => $this->get('attrs', []),
                'content'   => $this->get('content')
            ]
        );
    }
}