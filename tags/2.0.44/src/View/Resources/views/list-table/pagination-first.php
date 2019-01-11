<?php
/**
 * Pagination - Accès à la première page.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\View\Pattern\ListTable\Viewer\Viewer $this
 * @var tiFy\View\Pattern\ListTable\Pagination\Pagination $pagination
 * @var boolean $disabled
 * @var string $url
 */
?>
<?php
if ($this->get('disabled')) :
    echo partial(
        'tag',
        [
            'tag'     => 'span',
            'attrs'   => [
                'class'       => 'tablenav-pages-navspan',
                'aria-hidden' => 'true',
            ],
            'content' => '&laquo;',
        ]
    );
else :
    echo partial(
        'tag',
        [
            'tag'     => 'a',
            'attrs'   => [
                'class' => 'first-page',
                'href'  => $this->get('url'),
            ],
            'content' => sprintf(
                "<span class=\"screen-reader-text\">%s</span><span aria-hidden=\"true\">%s</span>",
                __('Première page', 'tify'),
                '&laquo;'
            ),
        ]
    );
endif;
