<?php

namespace tiFy\Partial\Partials\Pagination;

use tiFy\Contracts\Db\DbItemQueryInterface;
use tiFy\Partial\PartialController;
use WP_Query;

class Pagination extends PartialController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
     *      @var boolean|string $first Activation du lien vers la première page ou intitulé du lien.
     *      @var boolean|string $last Activation du lien vers la dernière page ou intitulé du lien.
     *      @var boolean|string $previous Activation du lien vers la page précédente ou intitulé du lien.
     *      @var boolean|string $next Activation du lien vers la page suivante ou intitulé du lien.
     *      @var boolean|array $numbers Activation de l'affichage de la numérotation des pages ou attributs d'affichage {
     *          Liste des attributs d'affichage.
     *
     *          @var int $range
     *          @var int $anchor
     *          @var int $gap
     * }
     *      @var object|false $query Instance de la classe de traitement des requêtes. \WP_Query par défaut
     *      @var int $per_page Nombre d'élément affiché par page.
     *      @var int page Numéro de la page courante.
     * }
     */
    protected $attributes = [
        'before'   => '',
        'after'    => '',
        'attrs'    => [],
        'viewer'   => [],
        'first'    => '&laquo;',
        'last'     => '&raquo;',
        'previous' => '&lsaquo;',
        'next'     => '&rsaquo;',
        'numbers'  => [
            'range'  => 2,
            'anchor' => 3,
            'gap'    => 1,
        ],
        'query'    => false,
        'per_page' => 0,
        'page'     => 0,
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action(
            'init',
            function () {
                wp_register_style(
                    'PartialPagination',
                    assets()->url('partial/pagination/css/styles.css'),
                    [],
                    181005
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        /** @var WP_Query $wp_query */
        global $wp_query;

        return [
            'query' => $wp_query,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue_scripts()
    {
        wp_enqueue_style('PartialPagination');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $query = $this->get('query');

        if (!$this->get('page')) :
            $this->set(
                'page',
                intval(
                    $query instanceof WP_Query
                        ? ($query->get('paged') ?: 1)
                        : 1
                )
            );
        endif;

        if (!$this->get('per_page')) :
            $this->set(
                'per_page',
                intval(
                    $query instanceof WP_Query
                        ? $query->get('posts_per_page', get_option('posts_per_page'))
                        : ($query instanceof DbItemQueryInterface ? $query->get('per_page', 10) : 10)
                )
            );
        endif;

        $offset = intval(
            $query instanceof WP_Query || $query instanceof DbItemQueryInterface
                ? $query->get('offset', 0) : 0
        );

        $found = intval(
            $query instanceof WP_Query
                ? $query->found_posts
                : ($query instanceof DbItemQueryInterface ? $query->found_items : 0)
        );

        if ($found) :
            $total = $offset
                ? ceil(
                    ($found + (($this->get('per_page') * ($this->get('page') - 1)) - $offset))
                    / $this->get('per_page')
                )
                : ceil($found / $this->get('per_page'));
        else :
            $total = 0;
        endif;
        $this->set('total', intval($total));

        if ($this->get('first')) :
            $this->set('first_url', $this->getPagenumLink(1));
        endif;

        if ($this->get('last')) :
            $this->set('last_url', $this->getPagenumLink($this->get('total')));
        endif;

        if ($this->get('previous')) :
            $this->set('previous_url', $this->getPagenumLink($this->get('page') - 1));
        endif;

        if ($this->get('next')) :
            $this->set('next_url', $this->getPagenumLink($this->get('page') + 1));
        endif;

        if ($this->get('numbers')) :
            $range = intval($this->get('numbers.range'));
            $anchor = intval($this->get('numbers.anchor'));
            $gap = intval($this->get('numbers.gap'));

            $min_links = ($range * 2) + 1;
            $block_min = min($this->get('page') - $range, $this->get('total') - $min_links);
            $block_high = max($this->get('page') + $range, $min_links);

            $this->set('numbers.block_min', $block_min);
            $this->set('numbers.block_high', $block_high);

            $this->set(
                'numbers.left_gap',
                (($block_min - $anchor - $gap) > 0)
                    ? true : false
            );
            $this->set(
                'numbers.right_gap',
                (($block_high + $anchor + $gap) < $this->get('total'))
                    ? true : false
            );
        endif;

        $this->viewer()->setController(PaginationView::class);
    }

    /**
     * Récupération du lien vers une page via son numéro.
     *
     * @param int $num Numéro de la page.
     *
     * @return string
     */
    public function getPagenumLink($num)
    {
        $query = $this->get('query');
        if ($query instanceof WP_Query) :
            return get_pagenum_link($num);
        elseif ($query instanceof DbItemQueryInterface) :
            /** @todo */
            return '';
        endif;

        return '';
    }
}