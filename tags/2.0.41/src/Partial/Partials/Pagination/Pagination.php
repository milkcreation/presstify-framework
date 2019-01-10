<?php

namespace tiFy\Partial\Partials\Pagination;

use tiFy\Contracts\Partial\Pagination as PaginationContract;
use tiFy\Partial\PartialController;

class Pagination extends PartialController implements PaginationContract
{
    /**
     * Liste des attributs de configuration.
     * @var array $attributes {
     *      @var string $before Contenu placé avant.
     *      @var string $after Contenu placé après.
     *      @var array $attrs Attributs de balise HTML.
     *      @var array $viewer Attributs de configuration du controleur de gabarit d'affichage.
     *      @var array $links {
     *          @var boolean|array $first Activation du lien vers la première page|Liste d'attributs.
     *          @var boolean|array $last Activation du lien vers la dernière page|Liste d'attributs.
     *          @var boolean|array $previous Activation du lien vers la page précédente|Liste d'attributs.
     *          @var boolean|array $next Activation du lien vers la page suivante|Liste d'attributs.
     *          @var boolean|array $numbers Activation de l'affichage de la numérotation des pages|Liste d'attributs {
     *              @var int $range
     *              @var int $anchor
     *              @var int $gap
     *          }
     *      }
     *      @var array|PaginationQuery|object $query Arguments de requête|Instance du controleur de traitement
     *                                               des requêtes.
     *      @var PaginationUrl|string $url Url de lien vers les pages. %d correspond au numéro de page.
     * }
     */
    protected $attributes = [
        'before'   => '',
        'after'    => '',
        'attrs'    => [],
        'viewer'   => [],
        'links'    => [
            'first'    => true,
            'last'     => true,
            'previous' => true,
            'next'     => true,
            'numbers'  => true
        ],
        'query'    => [],
        'url'      => ''
    ];

    /**
     * @var PaginationQuery
     */
    protected $query;

    /**
     * @var PaginationUrl
     */
    protected $url;

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

        $this->set('attrs.class', sprintf($this->get('attrs.class', '%s'), 'PartialPagination'));

        $this->url = $this->get('url', []);
        if (!$this->url instanceof PaginationUrl) :
            $this->url = new PaginationUrl($this->url);
        endif;
        $this->set('url', $this->url);

        $this->query = $this->get('query', []);
        if (!$this->query instanceof PaginationQuery) :
            $this->query = new PaginationQuery($this->query);
        endif;
        $this->set('query', $this->query);

        $this->parseLinks();

        if ($this->get('links.numbers')) :
            $this->parseNumbers();
        endif;

        $this->viewer()->setController(PaginationView::class);
    }

    /**
     * {@inheritdoc}
     */
    public function parseDefaults()
    {
        foreach($this->get('view', []) as $key => $value) :
            $this->viewer()->set($key, $value);
        endforeach;
    }

    /**
     * {@inheritdoc}
     */
    public function parseLinks()
    {
        $defaults = [
            'first'    => [
                'tag'     => 'a',
                'content' => '&laquo;',
                'attrs'   => [
                    'class' => 'PartialPagination-itemPage PartialPagination-itemPage--link',
                    'href'  => $this->url->page(1),
                ]
            ],
            'last'     => [
                'tag'     => 'a',
                'content' => '&raquo;',
                'attrs'   => [
                    'class' => 'PartialPagination-itemPage PartialPagination-itemPage--link',
                    'href'  => $this->url->page($this->query->getTotalPage()),
                ]
            ],
            'previous' => [
                'tag'     => 'a',
                'content' => '&lsaquo;',
                'attrs'   => [
                    'class' => 'PartialPagination-itemPage PartialPagination-itemPage--link',
                    'href'  => $this->url->page($this->query->getPage() - 1),
                ]
            ],
            'next'     => [
                'tag'     => 'a',
                'content' => '&rsaquo;',
                'attrs'   => [
                    'class' => 'PartialPagination-itemPage PartialPagination-itemPage--link',
                    'href'  => $this->url->page($this->query->getPage() + 1),
                ]
            ]
        ];

        foreach($defaults as $link => $default) :
            $attrs = $this->get("links.{$link}", []);

            if ($attrs === false) :
            elseif ($attrs === true) :
                $attrs = $default;
            else :
                $attrs = array_merge($this->get("links.{$link}", []), $default);
            endif;

            $this->set("links.{$link}", $attrs);
        endforeach;
    }

    /**
     * Traitement de la liste des numéros de page.
     *
     * @return void
     */
    public function parseNumbers()
    {
        $range = intval($this->get('links.numbers.range', 2));
        $anchor = intval($this->get('links.numbers.anchor', 3));
        $gap = intval($this->get('links.numbers.gap', 1));

        $min_links = ($range * 2) + 1;
        $block_min = min($this->query->getPage() - $range, $this->query->getTotalPage() - $min_links);
        $block_high = max($this->query->getPage() + $range, $min_links);

        $left_gap = (($block_min - $anchor - $gap) > 0) ? true : false;
        $right_gap = (($block_high + $anchor + $gap) < $this->query->getTotalPage()) ? true : false;

        $numbers = [];
        if ($left_gap && !$right_gap) :
            $this->numLoop($numbers, 1, $anchor);
            $this->ellipsis($numbers);
            $this->numLoop($numbers, $block_min, $this->query->getTotalPage());
        elseif ($left_gap && $right_gap) :
            $this->numLoop($numbers, 1, $anchor);
            $this->ellipsis($numbers);
            $this->numLoop($numbers, $block_min, $block_high);
            $this->ellipsis($numbers);
            $this->numLoop($numbers, ($this->query->getTotalPage()-$anchor+1), $this->query->getTotalPage());
        elseif (!$left_gap && $right_gap) :
            $this->numLoop($numbers, 1, $block_high);
            $this->ellipsis($numbers);
            $this->numLoop($numbers, ($this->query->getTotalPage()-$anchor+1), $this->query->getTotalPage());
        else :
            $this->numLoop($numbers, 1, $this->query->getTotalPage());
        endif;

        $this->set('numbers', $numbers);
    }

    /**
     * Boucle de récupération des numéros de page.
     *
     * @param array $numbers Liste des numéros de page existants.
     * @param int $start Démarrage de la boucle de récupération.
     * @param int $end Fin de la boucle de récupération.
     *
     * @return void
     */
    public function numLoop(&$numbers, $start, $end)
    {
        for ($num = $start; $num <= $end; $num++) :
            if ($num == 1 && !$this->query->getPage()) :
                $current = 'true';
            elseif ($this->query->getPage() == $num) :
                $current = 'true';
            else :
                $current = 'false';
            endif;

            $numbers[] = [
                'tag'     => 'a',
                'content' => $num,
                'attrs'   => [
                    'class'        => 'PartialPagination-itemPage PartialPagination-itemPage--link',
                    'href'         => $this->url->page($num),
                    'aria-current' => $current
                ]
            ];
        endfor;
    }

    /**
     * Récupération d'un séparateur de nombre.
     *
     * @param array $numbers Liste des numéros de page existants.
     *
     * @return void
     */
    public function ellipsis(&$numbers)
    {
        $numbers[] = [
            'tag' => 'span',
            'content' => '...',
            'attrs' => 'PartialPagination-itemEllipsis'
        ];
    }
}