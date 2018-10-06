<?php
/**
 * Pagination - Lien vers la page suivante.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Partial\Pagination\PaginationView $this
 */
?>

<?php if ($this->get('page') < $this->get('total')) : ?>
    <li class="Pagination-item Pagination-item--next">
        <?php
        echo partial(
            'tag',
            [
                'tag' => 'a',
                'attrs' => [
                    'class' => 'Pagination-itemPage Pagination-itemPage--link',
                    'href' => $this->get('next_url')
                ],
                'content' => $this->get('next')
            ]
        );
        ?>
    </li>
<?php endif;