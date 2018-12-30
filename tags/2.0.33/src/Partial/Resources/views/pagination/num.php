<?php
/**
 * Pagination - Lien vers un numéro de page.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Partial\Partials\Pagination\PaginationView $this
 */
?>

<li class="Pagination-item Pagination-item--num"
    aria-current="<?php echo $this->get('page') === $this->get('num') ? 'true' : 'false'?>"
>
    <?php
    echo partial(
        'tag',
        [
            'tag' => 'a',
            'attrs' => [
                'class' => 'Pagination-itemPage Pagination-itemPage--link',
                'href' => $this->getPagenumLink($this->get('num'))
            ],
            'content' => $this->get('num')
        ]
    );
    ?>
</li>