<?php

namespace tiFy\Partial\Pagination;

use tiFy\Contracts\Db\DbItemQueryInterface;
use tiFy\Partial\AbstractPartialItem;
use \WP_Query;

class Pagination extends AbstractPartialItem
{
    /**
     * Liste des attributs de configuration.
     * @var array {
     * @var string $theme Apparence. light|dark.
     * @var boolean|string $first Activation du lien vers la première page ou intitulé du lien.
     * @var boolean|string $last Activation du lien vers la dernière page ou intitulé du lien.
     * @var boolean|string $previous Activation du lien vers la page précédente ou intitulé du lien.
     * @var boolean|string $next Activation du lien vers la page suivante ou intitulé du lien.
     * @var boolean $numbers Activation de l'affichage de la numérotation des pages.
     * @var int $range
     * @var int $anchor
     * @var int $gap
     * @var object|false $query Instance de la classe de traitement des requêtes. \WP_Query par défaut
     * @var int $per_page Nombre d'élément affiché par page.
     * @var int page Numéro de la page courante.
     * }
     */
    protected $attributes = [
        'theme'    => 'light',
        'first'    => '&laquo;&laquo;',
        'last'     => '&raquo;&raquo;',
        'previous' => '&laquo;',
        'next'     => '&raquo;',
        'numbers'  => true,
        'range'    => 2,
        'anchor'   => 3,
        'gap'      => 1,
        'query'    => false,
        'per_page' => 0,
        'paged'    => 0
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
            'query' => $wp_query
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

        if (!$this->get('paged')) :
            $this->set(
                'paged',
                intval(
                    $query instanceof WP_Query
                        ? $query->get('paged', 1)
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
                        ($found+(($this->get('per_page')*($this->get('paged')-1))-$offset))
                        /$this->get('per_page')
                    )
                : ceil($found/$this->get('per_page'));
        else :
            $total = 0;
        endif;
        $this->set('total', intval($total));

        $this->set('prev_url', $this->getPagenumLink($this->get('paged')-1));
        $this->set('next_url', $this->getPagenumLink($this->get('paged')+1));

        if ($this->get('numbers')) :
            $range = intval($this->get('range'));
            $anchor = intval($this->get('anchor'));
            $gap = intval($this->get('gap'));


            $min_links = ($range*2)+1;
            $block_min = min($this->get('paged')-$range, $this->get('total')-$min_links);
            $block_high = max($this->get('paged')+$range, $min_links);

            $this->set(
                'left_gap',
                (($block_min-$anchor-$gap)>0)
                    ? true : false
            );
            $this->set(
                'right_gap',
                (($block_high+$anchor+$gap)<$this->get('total'))
                    ? true : false
            );
        else :
            $this->set('left_gap', false);
            $this->set('right_gap', false);
        endif;
    }

    /**
     *
     */
    public function getPagenumLink($num)
    {
        $query = $this->get('query');
        if ($query instanceof WP_Query) :
            return get_pagenum_link($num);
        elseif($query instanceof DbItemQueryInterface) :
            return '';
        endif;

        return '';
    }

    /** == == **/
    static private function loop($start, $max, $paged = 0)
    {
        $output = "";
        for ($i = $start; $i <= $max; $i++) {
            $output .= ($paged == intval($i)) ? "\t<li class=\"tiFyPagination-Item tiFyPagination-Item--active\"><span class=\"tiFyPagination-ItemPage\">{$i}</span></li>\n" : "\t<li class=\"tiFyPagination-Item tiFyPagination-Item--nav\"><a href=\"" . esc_url(get_pagenum_link($i)) . "\" class=\"tiFyPagination-ItemPage tiFyPagination-ItemPage--link\">{$i}</a></li>\n";
        }

        return $output;
    }
}