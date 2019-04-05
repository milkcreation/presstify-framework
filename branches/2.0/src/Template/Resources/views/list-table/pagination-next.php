<?php
/**
 * Pagination - Accès à la page suivante.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\ListTable\Viewer\Viewer $this
 * @var tiFy\Template\Templates\ListTable\Pagination\Pagination $pagination
 * @var boolean $disabled
 * @var string $url
 */
?>
<?php
if ($this->get('disabled')) :
    echo partial('tag', [
        'tag'     => 'span',
        'attrs'   => [
            'class'       => 'tablenav-pages-navspan',
            'aria-hidden' => 'true',
        ],
        'content' => '&rsaquo;',
    ]);
else :
    echo partial('tag', [
        'tag'     => 'a',
        'attrs'   => [
            'class' => 'next-page',
            'href'  => $this->get('url'),
        ],
        'content' => sprintf(
            "<span class=\"screen-reader-text\">%s</span><span aria-hidden=\"true\">%s</span>",
            __('Page suivante', 'tify'),
            '&rsaquo;'
        ),
    ]);
endif;