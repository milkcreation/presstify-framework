<?php
/**
 * Pagination - Lien vers la page précédente.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Partial\Partials\Pagination\PaginationView $this
 */
?>

<?php if ($this->get('page') > 1) : ?>
    <li class="Pagination-item Pagination-item--previous">
        <?php
        echo partial(
            'tag',
            [
                'tag' => 'a',
                'attrs' => [
                    'class' => 'Pagination-itemPage Pagination-itemPage--link',
                    'href' => $this->get('previous_url')
                ],
                'content' => $this->get('previous')
            ]
        );
        ?>
    </li>
<?php endif;