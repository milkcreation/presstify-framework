<?php
/**
 * Pagination - Lien vers la première page.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Partial\PartialView $this
 */
?>

<?php if ($this->get('paged') > 1) : ?>
<li class="Pagination-item Pagination-item--first">
    <?php
    echo partial(
        'tag',
        [
            'tag' => 'a',
            'attrs' => [
                'class' => 'Pagination-itemPage Pagination-itemPage--link',
                'href' => $this->get('first_url')
            ],
            'content' => $this->get('first')
        ]
    );
    ?>
</li>
<?php endif;