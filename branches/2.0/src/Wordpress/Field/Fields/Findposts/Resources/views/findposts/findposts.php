<?php
/**
 * Field Findposts.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Field\FieldView $this
 */
?>
<?php $this->before(); ?>

<div aria-control="findposts">
    <?php
    echo field(
        'text',
        [
            'attrs' => $this->get('attrs', [])
        ]
    );
    echo field('button');
    ?>
</div>

<?php $this->after();