<?php
/**
 * Pagination - Lien vers la dernière page.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Partial\PartialView $this
 */
?>

<?php if ($this->get('paged') < $this->get('total')) : ?>
<li class="Pagination-item Pagination-item--last">
    <?php
    echo partial(
        'tag',
        [
            'tag' => 'a',
            'attrs' => [
                'class' => 'Pagination-itemPage Pagination-itemPage--link',
                'href' => $this->get('last_url')
            ],
            'content' => $this->get('last')
        ]
    );
    ?>
</li>
<?php endif;